{extends file="parent:frontend/address/form.tpl"}

{block name='frontend_address_form_input_street'}
    {$smarty.block.parent}
    
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {include file='frontend/includes/splitted_street.tpl' inputPrefix=$inputPrefix formData=$formData}

        {foreach from=['Address', 'Phone', 'Person'] item=infix}
            {include file='frontend/includes/sdk_meta_fields.tpl' inputPrefix=$inputPrefix infix=$infix formData=$formData}
        {/foreach}
    {/if}
{/block}
