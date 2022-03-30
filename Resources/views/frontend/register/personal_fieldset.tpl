{extends file="parent:frontend/register/personal_fieldset.tpl"}

{block name='frontend_register_personal_fieldset_input_firstname'}
    {$smarty.block.parent}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        <script>
            ( function() {
                var $interval = setInterval( function() {
                    if (window.EnderecoIntegrator && window.EnderecoIntegrator.ready) {
                        window.EnderecoIntegrator.initPersonServices('register[personal]', {
                            name: 'general',
                        });
                        clearInterval($interval);
                    }
                }, 100);
            })();
        </script>
    {/if}
{/block}


{block name='frontend_register_personal_fieldset_input_mail'}
    {$smarty.block.parent}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        <script>
            ( function() {
                var $interval = setInterval( function() {
                    if (window.EnderecoIntegrator && window.EnderecoIntegrator.ready) {
                        window.EnderecoIntegrator.initEmailServices('register[personal]');
                        clearInterval($interval);
                    }
                }, 100);
            })();
        </script>
    {/if}
{/block}

{block name='frontend_register_personal_fieldset_input_phone'}
    {$smarty.block.parent}
    {if {config name="showPhoneNumberField"}}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        <script>
          ( function() {
            var $interval = setInterval( function() {
              if (window.EnderecoIntegrator && window.EnderecoIntegrator.ready) {
                window.EnderecoIntegrator.initPhoneServices(
                  'register[personal]',
                  {
                    'name': 'general'
                  }
                );
                clearInterval($interval);
              }
            }, 100);
          })();
        </script>
    {/if}

{/if}
{/block}
