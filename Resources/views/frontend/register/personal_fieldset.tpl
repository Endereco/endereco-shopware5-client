{extends file="parent:frontend/register/personal_fieldset.tpl"}

{block name='frontend_register_personal_fieldset_input_lastname'}
    {$smarty.block.parent}
{/block}


{block name='frontend_register_personal_fieldset_input_mail'}
    {$smarty.block.parent}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {include file='frontend/includes/sdk_meta_fields.tpl' inputPrefix='email' infix='Email' formData=$form_data}
    {/if}
{/block}

{block name='frontend_register_personal_fieldset_input_phone'}
    {$smarty.block.parent}
{/block}
