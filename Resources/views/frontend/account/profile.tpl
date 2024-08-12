{extends file="parent:frontend/account/profile.tpl"}

{block name='frontend_account_profile_profile_input_firstname'}
    {$smarty.block.parent}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        <script>
            ( function() {
                var $interval = setInterval( function() {
                    if (window.EnderecoIntegrator && window.EnderecoIntegrator.ready) {
                        window.EnderecoIntegrator.initPersonServices('profile');
                        clearInterval($interval);
                    }
                }, 100);
            })();
        </script>
    {/if}
    <input type="hidden" name="profile[attribute][endereco_status]" value="{$form_data.attribute.endereco_status}" />
    <input type="hidden" name="profile[attribute][endereco_predictions]" value="{$form_data.attribute.endereco_predictions}" />
    <input type="hidden" name="profile[attribute][endereco_hash]" value="{$form_data.attribute.endereco_hash}" />
    <input type="hidden" name="profile[attribute][endereco_session_id]" value="{$form_data.attribute.endereco_session_id}" />
    <input type="hidden" name="profile[attribute][endereco_session_counter]" value="{$form_data.attribute.endereco_session_counter}" />
{/block}

{block name='frontend_account_profile_email_input_email'}
    <input type="hidden" name="email[attribute][endereco_status]" value="{$form_data.attribute.endereco_status}" />
    <input type="hidden" name="email[attribute][endereco_predictions]" value="{$form_data.attribute.endereco_predictions}" />
    <input type="hidden" name="email[attribute][endereco_hash]" value="{$form_data.attribute.endereco_hash}" />
    <input type="hidden" name="email[attribute][endereco_session_id]" value="{$form_data.attribute.endereco_session_id}" />
    <input type="hidden" name="email[attribute][endereco_session_counter]" value="{$form_data.attribute.endereco_session_counter}" />
{/block}
