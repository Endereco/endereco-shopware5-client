<?php

namespace EnderecoShopware5Client\Strategies;

use Shopware\Bundle\AttributeBundle\Service\TypeMapping;
use Shopware\Bundle\AttributeBundle\Service\CrudService;

class MetaAndSessionAttributes
{
    /**
     * @var array Collection of desired attribute names and their corresponding details.
     **/
    protected $attributesInfos = [
        'status' => [
            'label' => 'status_of_the_validation',
            'displayInBackend' => true,
            'type' => TypeMapping::TYPE_STRING
        ],
        'predictions' => [
            'label' => 'json_with_results',
            'displayInBackend' => false,
            'type' => TypeMapping::TYPE_STRING,
        ],
        'hash' => [
            'label' => 'hash_of_the_data',
            'displayInBackend' => true,
            'type' => TypeMapping::TYPE_STRING,
        ],
        'session_id' => [
            'label' => 'session_id',
            'displayInBackend' => false,
            'type' => TypeMapping::TYPE_STRING,
        ],
        'session_counter' => [
            'label' => 'session_counter',
            'displayInBackend' => false,
            'type' => TypeMapping::TYPE_INTEGER,
        ],
    ];

    /**
     * @var array A mapping of infix types (e.g., 'person', 'email') to associated table names.
     **/
    protected $infixesToTablesMap = [
        'person' => [
            's_user_addresses_attributes',
            's_order_shippingaddress_attributes',
            's_order_billingaddress_attributes'
        ],
        'email' => ['s_user_attributes'],
        'profile' => ['s_user_attributes'],
        'phone' => [
            's_user_addresses_attributes',
            's_order_shippingaddress_attributes',
            's_order_billingaddress_attributes'
        ],
        'address' => [
            's_user_addresses_attributes',
            's_order_shippingaddress_attributes',
            's_order_billingaddress_attributes'
        ]
    ];

    /**
     * Placeholder method for executing the strategy.
     *
     * @param CrudService $crud The CRUD service used for attribute operations.
     *
     * @throws \RuntimeException Always throws an exception as this method should be implemented in a subclass.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(CrudService $crud)
    {
        throw new \RuntimeException(
            "This class should not be used directly, instead extend it and implement custom 'execute' method."
        );
    }

    /**
     * Generates all possible attribute combinations based on the infixes and attribute information.
     *
     * @param string $versionSuffix A suffix to append to the attribute names.
     *
     * @return array An array of attribute definitions including table name, attribute name, label,
     *               type, and other metadata.
     */
    protected function generateAllAttributeCombinations($versionSuffix)
    {
        $attributes = [];
        foreach ($this->infixesToTablesMap as $infix => $relatedTables) {
            foreach ($relatedTables as $tableName) {
                foreach ($this->attributesInfos as $name => $infos) {
                    $attributes[] = [
                        'tableName' => $tableName,
                        'attributeName' => "endereco_{$infix}_{$name}_{$versionSuffix}",
                        'label' => $infos['label'],
                        'type' => $infos['type'],
                        'displayInBackend' => $infos['displayInBackend'],
                        'translateable' => true
                    ];
                }
            }
        }
        return $attributes;
    }

    /**
     * Creates or updates attributes from the list in the database.
     *
     * @param array $attributeDataList A list of attribute data arrays to create or update.
     * @param CrudService $crud The CRUD service used for attribute operations.
     *
     * @return void
     */
    protected function createOrUpdateAttributes(array $attributeDataList, CrudService $crud)
    {
        foreach ($attributeDataList as $attributeData) {
            if (!$crud->get($attributeData['tableName'], $attributeData['attributeName'])) {
                $crud->update(
                    $attributeData['tableName'],
                    $attributeData['attributeName'],
                    $attributeData['type'],
                    [
                        'label' => $attributeData['label'],
                        'displayInBackend' => $attributeData['displayInBackend'],
                        'custom' => true // Todo: discuss if its really needed
                    ]
                );
            }
        }
    }

    /**
     * Removes attributes in the list from the database.
     *
     * @param array $attributeDataList A list of attribute data arrays to be removed.
     * @param CrudService $crud The CRUD service used for attribute operations.
     *
     * @return void
     */
    protected function removeAttributes($attributeDataList, $crud)
    {
        foreach ($attributeDataList as $attributeData) {
            if ($crud->get($attributeData['tableName'], $attributeData['attributeName'])) {
                $crud->delete($attributeData['tableName'], $attributeData['attributeName']);
            }
        }
    }

    /**
     * Flushes the metadata cache and regenerates attribute models.
     *
     * @return void
     */
    protected function flush()
    {
        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        if (method_exists($metaDataCache, 'deleteAll')) {
            $metaDataCache->deleteAll();
        }
        Shopware()->Models()->generateAttributeModels(
            [
                's_user_attributes',
                's_user_addresses_attributes',
                's_order_attributes',
                's_order_billingaddress_attributes',
                's_order_shippingaddress_attributes'
            ]
        );
    }
}
