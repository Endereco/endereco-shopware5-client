<?php
namespace EnderecoShopware5Client\Services;

use GuzzleHttp\Client;
use Shopware\Models\Country\Country;
use Shopware\Models\Customer\Address;
use Shopware\Models\Customer\AddressRepository;
use GuzzleHttp\Exception\RequestException;

class EnderecoService {
    private $logger;
    private $pluginInfo;
    private $apiKey;
    private $httpClient;
    private $info;
    private $serviceUrl;
    private $version;

    public function __construct($pluginInfo, $logger) {
        $this->pluginInfo = $pluginInfo;
        $this->logger = $logger;
        $this->httpClient = new Client(['timeout' => 3.0, 'connection_timeout' => 2.0]);

        $config = Shopware()->Container()->get('config');
        $this->apiKey = $config->get('apiKey');
        $this->info = 'Endereco Shopware5 Client (Download) v'.$this->pluginInfo['version'];
        $this->serviceUrl = $config->get('remoteApiUrl');
        $this->version = $this->pluginInfo['version'];
    }

    public function getVersion() {
        return $this->version;
    }

    public function generateTid() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function checkAddresses($addressIdArray = array()) {
        $checkedAddressesCounter = 0;
        if(!$addressIdArray) {
            return;
        }
        $accountableSessions = array();

        // Cant check address if there is no api key.
        if (!$this->apiKey) {
            return;
        }

        /**
         * @var AddressRepository
         */
        $addressRepository = Shopware()->Models()->getRepository(Address::class);
        // For each address.
        foreach ($addressIdArray as $addressId) {

            // Generate session id.
            $tid = $this->generateTid();

            // Fetch addressdata from database.
            $addressArray = array();
            try {
                $address = $addressRepository->getOne($addressId)->getOneOrNullResult();
                $addressArray = Shopware()->Models()->toArray($address);

                $countryRepository = Shopware()->Models()->getRepository(Country::class);
                $countryCode = strtolower($countryRepository->find($addressArray['country'])->getIso());

                $locale = Shopware()->Container()->get('Shop')->getLocale()->getLocale();
                $languageCode = explode('_', $locale)[0];
            } catch(\Exception $e) {
                $this->logger->addError($e->getMessage());
                continue;
            }

            // Send request to endereco server.
            if ($addressArray) {
                try {
                    $message = array(
                        'jsonrpc' => '2.0',
                        'id' => 1,
                        'method' => 'addressCheck',
                        'params' => array(
                            'country' => $countryCode,
                            'language' => $languageCode,
                            'postCode' => $addressArray['zipcode'],
                            'cityName' => $addressArray['city'],
                            'streetFull' => $addressArray['street']
                        )
                    );

                    $newHeaders = array(
                        'Content-Type' => 'application/json',
                        'X-Auth-Key' => $this->apiKey,
                        'X-Transaction-Id' => $tid,
                        'X-Transaction-Referer' => $_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:__FILE__,
                        'X-Agent' => $this->info,
                    );

                    $checkResponse = $this->httpClient->post(
                        $this->serviceUrl,
                        array(
                            'headers' => $newHeaders,
                            'body' => json_encode($message)
                        )
                    );
                    $result = json_decode($checkResponse->getBody(), true);
                    if (array_key_exists('result', $result)) {
                        // Add sesssion id to a list of accountable session ids.
                        $accountableSessions[] = $tid;

                        // Create an array of predictions.
                        $predictions = array();
                        $maxPredictions = 3;
                        $counter = 0;
                        foreach ($result['result']['predictions'] as $prediction) {
                            $tempAddress = array(
                                'countryCode' => $prediction['countryCode']?$prediction['countryCode']:$countryCode,
                                'postalCode' => $prediction['postCode'],
                                'locality' => $prediction['cityName'],
                                'streetName' => $prediction['street'],
                                'buildingNumber' => $prediction['houseNumber']
                            );
                            if (array_key_exists('additionalInfo', $prediction)) {
                                $tempAddress['additionalInfo'] = $prediction['additionalInfo'];
                            }

                            $predictions[] = $tempAddress;
                            $counter++;
                            if ($counter >= $maxPredictions) {
                                break;
                            }
                        }

                        // Create an array of statuses.
                        $statuses = array();
                        if (
                            in_array('A1000', $result['result']['status']) &&
                            !in_array('A1100', $result['result']['status'])
                        ) {
                            $statuses[] = 'address_correct';
                        }
                        if (
                            in_array('A1000', $result['result']['status']) &&
                            in_array('A1100', $result['result']['status'])
                        ) {
                            $statuses[] = 'address_needs_correction';
                        }
                        if (
                            in_array('A2000', $result['result']['status'])
                        ) {
                            $statuses[] = 'address_multiple_variants';
                        }
                        if (
                            in_array('A3000', $result['result']['status'])
                        ) {
                            $statuses[] = 'address_not_found';
                        }
                        if (
                            in_array('A3100', $result['result']['status'])
                        ) {
                            $statuses[] = 'address_is_packstation';
                        }

                        // Create timestamp.
                        $timestamp = time();

                        // Save the status and predictions.
                        if ($address) {
                            $attribute = $address->getAttribute();
                            $attribute->setEnderecoamsts($timestamp);
                            $attribute->setEnderecoamsstatus(implode(',', $statuses));
                            $attribute->setEnderecoamsapredictions(json_encode($predictions));
                            Shopware()->Container()->get('shopware_account.address_service')->update($address);
                        }
                        $checkedAddressesCounter++;
                    }
                } catch (RequestException $e) {
                    if ($e->hasResponse()) {
                        $response = $e->getResponse();
                        if (500 <= $response->getStatusCode()) {
                            $this->logger->addError($e->getMessage());
                        }
                    }
                } catch(\Exception $e) {
                    $this->logger->addError($e->getMessage());
                }
            }
        }

        $this->sendDoAccountings($accountableSessions);

        return $checkedAddressesCounter;
    }

    public function sendDoAccountings($sessionIds = array()) {
        if (!$sessionIds) {
            return;
        }

        $anyDoAccounting = false;

        foreach ($sessionIds as $sessionId) {
            try {
                $message = array(
                    'jsonrpc' => '2.0',
                    'id' => 1,
                    'method' => 'doAccounting',
                    'params' => array(
                        'sessionId' => $sessionId
                    )
                );
                $newHeaders = array(
                    'Content-Type' => 'application/json',
                    'X-Auth-Key' => $this->apiKey,
                    'X-Transaction-Id' => $sessionId,
                    'X-Transaction-Referer' => $_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:__FILE__,
                    'X-Agent' => $this->info,
                );
                $this->httpClient->post(
                    $this->serviceUrl,
                    array(
                        'headers' => $newHeaders,
                        'body' => json_encode($message)
                    )
                );
                $anyDoAccounting = true;

            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    if (500 <= $response->getStatusCode()) {
                        $this->logger->addError($e->getMessage());
                    }
                }
            } catch(\Exception $e) {
                $this->logger->addError($e->getMessage());
            }
        }

        if ($anyDoAccounting) {
            try {
                $message = array(
                    'jsonrpc' => '2.0',
                    'id' => 1,
                    'method' => 'doConversion',
                    'params' => array()
                );
                $newHeaders = array(
                    'Content-Type' => 'application/json',
                    'X-Auth-Key' => $this->apiKey,
                    'X-Transaction-Id' => 'not_required',
                    'X-Transaction-Referer' => $_SERVER['HTTP_REFERER']?$_SERVER['HTTP_REFERER']:__FILE__,
                    'X-Agent' => $this->info,
                );
                $this->httpClient->post(
                    $this->serviceUrl,
                    array(
                        'headers' => $newHeaders,
                        'body' => json_encode($message)
                    )
                );
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    if (500 <= $response->getStatusCode()) {
                        $this->logger->addError($e->getMessage());
                    }
                }
            } catch(\Exception $e) {
                $this->logger->addError($e->getMessage());
            }
        }
    }
}
