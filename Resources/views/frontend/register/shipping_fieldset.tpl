{extends file="parent:frontend/register/shipping_fieldset.tpl"}

{block name='frontend_register_shipping_fieldset_input_firstname'}
    {$smarty.block.parent}
    <script>
        if (window.EnderecoIntegrator && window.EnderecoIntegrator.initPersonServices) {
            window.EnderecoIntegrator.waitUntilReady().then(function() {
                window.EnderecoIntegrator.initPersonServices('register[shipping]');
            });
        } else if (window.EnderecoIntegrator && !window.EnderecoIntegrator.initPersonServices && window.EnderecoIntegrator.asyncCallbacks) {
            window.EnderecoIntegrator.asyncCallbacks.push(function() {
                window.EnderecoIntegrator.waitUntilReady().then(function() {
                    window.EnderecoIntegrator.initPersonServices('register[shipping]');
                });
            });
        } else {
            window.EnderecoIntegrator = {
                asyncCallbacks: []
            };
            window.EnderecoIntegrator.asyncCallbacks.push(function() {
                window.EnderecoIntegrator.waitUntilReady().then(function() {
                    window.EnderecoIntegrator.initPersonServices('register[shipping]');
                });
            });
        }
    </script>
{/block}
