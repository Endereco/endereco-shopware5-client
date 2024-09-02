{extends file="parent:frontend/register/index.tpl"}

{block name='frontend_register_billing_fieldset_input_street'}
    {$smarty.block.parent}

    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {include file='frontend/includes/splitted_street.tpl' inputPrefix='register[billing]' formData=$formData}

        {foreach from=['Address', 'Phone', 'Person'] item=infix}
            {include file='frontend/includes/sdk_meta_fields.tpl' inputPrefix='register[billing]' infix=$infix formData=$form_data}
        {/foreach}
    {/if}
{/block}

{block name='frontend_register_shipping_fieldset_input_street'}
    {$smarty.block.parent}

    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {include file='frontend/includes/splitted_street.tpl' inputPrefix='register[shipping]' formData=$formData}

        {foreach from=['Address', 'Phone', 'Person'] item=infix}
            {include file='frontend/includes/sdk_meta_fields.tpl' inputPrefix='register[shipping]' infix=$infix formData=$form_data}
        {/foreach}
    {/if}
{/block}

