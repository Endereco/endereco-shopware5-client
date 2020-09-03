{extends file="parent:frontend/register/personal_fieldset.tpl"}

{block name='frontend_register_personal_fieldset_input_firstname'}
    {$smarty.block.parent}
    <script>
        if (window.EnderecoIntegrator && window.EnderecoIntegrator.initPersonServices) {
            window.EnderecoIntegrator.waitUntilReady().then(function() {
                window.EnderecoIntegrator.initPersonServices('register[personal]');
            });
        } else if (window.EnderecoIntegrator && !window.EnderecoIntegrator.initPersonServices && window.EnderecoIntegrator.asyncCallbacks) {
            window.EnderecoIntegrator.asyncCallbacks.push(function() {
                window.EnderecoIntegrator.waitUntilReady().then(function() {
                    window.EnderecoIntegrator.initPersonServices('register[personal]');
                });
            });
        } else {
            window.EnderecoIntegrator = {
                asyncCallbacks: []
            };
            window.EnderecoIntegrator.asyncCallbacks.push(function() {
                window.EnderecoIntegrator.waitUntilReady().then(function() {
                    window.EnderecoIntegrator.initPersonServices('register[personal]');
                });
            });
        }
    </script>
{/block}


{block name='frontend_register_personal_fieldset_input_mail'}
    {$smarty.block.parent}
    <script>
        if (window.EnderecoIntegrator && window.EnderecoIntegrator.initEmailServices) {
            window.EnderecoIntegrator.waitUntilReady().then(function() {
                window.EnderecoIntegrator.initEmailServices('register[personal]');
            });
        } else if (window.EnderecoIntegrator && !window.EnderecoIntegrator.initEmailServices && window.EnderecoIntegrator.asyncCallbacks) {
            window.EnderecoIntegrator.asyncCallbacks.push(function() {
                window.EnderecoIntegrator.waitUntilReady().then(function() {
                    window.EnderecoIntegrator.initEmailServices('register[personal]');
                });
            });
        } else {
            window.EnderecoIntegrator = {
                asyncCallbacks: []
            };
            window.EnderecoIntegrator.asyncCallbacks.push(function() {
                window.EnderecoIntegrator.waitUntilReady().then(function() {
                    window.EnderecoIntegrator.initEmailServices('register[personal]');
                });
            });
        }
    </script>
{/block}
