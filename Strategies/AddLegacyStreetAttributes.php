<?php

namespace EnderecoShopware5Client\Strategies;

use Shopware\Bundle\AttributeBundle\Service\CrudService;

/**
 * Class AddLegacyAttributes
 *
 * This class is responsible for adding legacy attributes such as street name
 * and building number to various entities in Shopware.
 *
 * @package EnderecoShopware5Client\Strategies
 */
class AddLegacyStreetAttributes extends MetaAndSessionAttributes
{
    /**
     * Executes the strategy to add legacy attributes.
     *
     * @param CrudService $crud The CRUD service used to manage attributes.
     * @return void
     */
    public function execute(CrudService $crud)
    {
        $this->addLegacyStreetNameAndBuildingNumber($crud);
        $this->flush();
    }

    /**
     * Adds legacy attributes for street name and building number to the specified entities.
     *
     * @param CrudService $crud The CRUD service used to manage attributes.
     * @return void
     */
    protected function addLegacyStreetNameAndBuildingNumber(CrudService $crud)
    {
        // Add 'enderecostreetname' attribute to 's_user_addresses_attributes'
        if (!$crud->get('s_user_addresses_attributes', 'enderecostreetname')) {
            $crud->update(
                's_user_addresses_attributes',
                'enderecostreetname',
                \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING,
                [
                    'label' => 'Straßenname',
                    'displayInBackend' => true,
                    'custom' => true
                ]
            );
        }

        // Add 'enderecobuildingnumber' attribute to 's_user_addresses_attributes'
        if (!$crud->get('s_user_addresses_attributes', 'enderecobuildingnumber')) {
            $crud->update(
                's_user_addresses_attributes',
                'enderecobuildingnumber',
                \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING,
                [
                    'label' => 'Hausnummer',
                    'displayInBackend' => true,
                    'custom' => true
                ]
            );
        }

        // Add 'enderecostreetname' attribute to 's_order_billingaddress_attributes'
        if (!$crud->get('s_order_billingaddress_attributes', 'enderecostreetname')) {
            $crud->update(
                's_order_billingaddress_attributes',
                'enderecostreetname',
                \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING,
                [
                    'label' => 'Straßenname',
                    'displayInBackend' => true,
                    'custom' => true
                ]
            );
        }

        // Add 'enderecobuildingnumber' attribute to 's_order_billingaddress_attributes'
        if (!$crud->get('s_order_billingaddress_attributes', 'enderecobuildingnumber')) {
            $crud->update(
                's_order_billingaddress_attributes',
                'enderecobuildingnumber',
                \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING,
                [
                    'label' => 'Hausnummer',
                    'displayInBackend' => true,
                    'custom' => true
                ]
            );
        }

        // Add 'enderecostreetname' attribute to 's_order_shippingaddress_attributes'
        if (!$crud->get('s_order_shippingaddress_attributes', 'enderecostreetname')) {
            $crud->update(
                's_order_shippingaddress_attributes',
                'enderecostreetname',
                \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING,
                [
                    'label' => 'Straßenname',
                    'displayInBackend' => true,
                    'custom' => true
                ]
            );
        }

        // Add 'enderecobuildingnumber' attribute to 's_order_shippingaddress_attributes'
        if (!$crud->get('s_order_shippingaddress_attributes', 'enderecobuildingnumber')) {
            $crud->update(
                's_order_shippingaddress_attributes',
                'enderecobuildingnumber',
                \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING,
                [
                    'label' => 'Hausnummer',
                    'displayInBackend' => true,
                    'custom' => true
                ]
            );
        }
    }
}
