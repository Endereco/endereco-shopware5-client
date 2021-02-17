{extends file="parent:frontend/register/shipping_fieldset.tpl"}

{block name='frontend_register_shipping_fieldset_input_firstname'}
    {$smarty.block.parent}
    {if $endereco_is_active}
        <script>
            ( function() {
                var $interval = setInterval( function() {
                    if (window.EnderecoIntegrator && window.EnderecoIntegrator.ready) {
                        window.EnderecoIntegrator.initPersonServices('register[shipping]');
                        clearInterval($interval);
                    }
                }, 100);
            })();
        </script>
    {/if}
{/block}
