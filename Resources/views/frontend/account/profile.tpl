{extends file="parent:frontend/account/profile.tpl"}

{block name='frontend_account_profile_profile_input_lastname'}
    {$smarty.block.parent}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {include file='frontend/includes/sdk_meta_fields.tpl' inputPrefix='profile' infix='Profile' formData=$form_data.profile}
    {/if}
{/block}

{block name='frontend_account_profile_email_input_email'}
    {$smarty.block.parent}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {include file='frontend/includes/sdk_meta_fields.tpl' inputPrefix='email' infix='Email' formData=$form_data.email}
    {/if}    
{/block}
