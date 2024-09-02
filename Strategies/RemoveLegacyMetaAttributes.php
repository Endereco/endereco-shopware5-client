<?php

namespace EnderecoShopware5Client\Strategies;

use Shopware\Bundle\AttributeBundle\Service\CrudService;

class RemoveLegacyMetaAttributes extends MetaAndSessionAttributes
{
    /**
     * Executes the removal of legacy meta attributes.
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
     * Removes legacy attributes from various database tables.
     *
     * @param CrudService $crud The CRUD service used for attribute operations.
     *
     * @return void
     */
    protected function removeLegacyAttributes($crud)
    {
        // Standard address table attributes.
        if ($crud->get('s_user_addresses_attributes', 'enderecoamsts')) {
            $crud->delete('s_user_addresses_attributes', 'enderecoamsts');
        }
        if ($crud->get('s_user_addresses_attributes', 'enderecoamsstatus')) {
            $crud->delete('s_user_addresses_attributes', 'enderecoamsstatus');
        }
        if ($crud->get('s_user_addresses_attributes', 'enderecoamsapredictions')) {
            $crud->delete('s_user_addresses_attributes', 'enderecoamsapredictions');
        }

        // Order billing address table attributes.
        if ($crud->get('s_order_billingaddress_attributes', 'enderecoamsts')) {
            $crud->delete('s_order_billingaddress_attributes', 'enderecoamsts');
        }
        if ($crud->get('s_order_billingaddress_attributes', 'enderecoamsstatus')) {
            $crud->delete('s_order_billingaddress_attributes', 'enderecoamsstatus');
        }
        if ($crud->get('s_order_billingaddress_attributes', 'enderecoamsapredictions')) {
            $crud->delete('s_order_billingaddress_attributes', 'enderecoamsapredictions');
        }

        // Order shipping address table attributes.
        if ($crud->get('s_order_shippingaddress_attributes', 'enderecoamsts')) {
            $crud->delete('s_order_shippingaddress_attributes', 'enderecoamsts');
        }
        if ($crud->get('s_order_shippingaddress_attributes', 'enderecoamsstatus')) {
            $crud->delete('s_order_shippingaddress_attributes', 'enderecoamsstatus');
        }
        if ($crud->get('s_order_shippingaddress_attributes', 'enderecoamsapredictions')) {
            $crud->delete('s_order_shippingaddress_attributes', 'enderecoamsapredictions');
        }

        // Attributes in the order table.
        if ($crud->get('s_order_attributes', 'endereco_order_billingamsts')) {
            $crud->delete('s_order_attributes', 'endereco_order_billingamsts');
        }
        if ($crud->get('s_order_attributes', 'endereco_order_shippingamsts')) {
            $crud->delete('s_order_attributes', 'endereco_order_shippingamsts');
        }
        if ($crud->get('s_order_attributes', 'endereco_order_billingamsstatus')) {
            $crud->delete('s_order_attributes', 'endereco_order_billingamsstatus');
        }
        if ($crud->get('s_order_attributes', 'endereco_order_shippingamsstatus')) {
            $crud->delete('s_order_attributes', 'endereco_order_shippingamsstatus');
        }
    }
}
