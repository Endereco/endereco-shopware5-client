{extends file="parent:frontend/account/profile.tpl"}

{block name='frontend_account_profile_profile_input_firstname'}
    {$smarty.block.parent}
    <script>
        if (window.EnderecoIntegrator && window.EnderecoIntegrator.initPersonServices) {
            window.EnderecoIntegrator.waitUntilReady().then(function() {
                window.EnderecoIntegrator.initPersonServices('profile');
            });
        } else if (window.EnderecoIntegrator && !window.EnderecoIntegrator.initPersonServices && window.EnderecoIntegrator.asyncCallbacks) {
            window.EnderecoIntegrator.asyncCallbacks.push(function() {
                window.EnderecoIntegrator.waitUntilReady().then(function() {
                    window.EnderecoIntegrator.initPersonServices('profile');
                });
            });
        } else {
            window.EnderecoIntegrator = {
                asyncCallbacks: []
            };
            window.EnderecoIntegrator.asyncCallbacks.push(function() {
                window.EnderecoIntegrator.waitUntilReady().then(function() {
                    window.EnderecoIntegrator.initPersonServices('profile');
                });
            });
        }
    </script>
{/block}
