<?php

namespace EnderecoShopware5Client\Strategies;

use Shopware\Bundle\AttributeBundle\Service\CrudService;

class RemoveLegacyStreetAttributes extends MetaAndSessionAttributes
{
    /**
     * Executes the removal of legacy street-related attributes.
     *
     * @param CrudService $crud The CRUD service used for attribute operations.
     *
     * @return void
     */
    public function execute(CrudService $crud)
    {
        $this->removeLegacyAttributes($crud);
        $this->flush();
    }

    /**
     * Removes legacy street attributes from various database tables.
     *
     * @param CrudService $crud The CRUD service used for attribute operations.
     *
     * @return void
     */
    protected function removeLegacyAttributes($crud)
    {
        // User address table attributes.
        if ($crud->get('s_user_addresses_attributes', 'enderecostreetname')) {
            $crud->delete('s_user_addresses_attributes', 'enderecostreetname');
        }
        if ($crud->get('s_user_addresses_attributes', 'enderecobuildingnumber')) {
            $crud->delete('s_user_addresses_attributes', 'enderecobuildingnumber');
        }

        // Order billing address table attributes.
        if ($crud->get('s_order_billingaddress_attributes', 'enderecostreetname')) {
            $crud->delete('s_order_billingaddress_attributes', 'enderecostreetname');
        }
        if ($crud->get('s_order_billingaddress_attributes', 'enderecobuildingnumber')) {
            $crud->delete('s_order_billingaddress_attributes', 'enderecobuildingnumber');
        }

        // Order shipping address table attributes.
        if ($crud->get('s_order_shippingaddress_attributes', 'enderecostreetname')) {
            $crud->delete('s_order_shippingaddress_attributes', 'enderecostreetname');
        }
        if ($crud->get('s_order_shippingaddress_attributes', 'enderecobuildingnumber')) {
            $crud->delete('s_order_shippingaddress_attributes', 'enderecobuildingnumber');
        }
    }
}
