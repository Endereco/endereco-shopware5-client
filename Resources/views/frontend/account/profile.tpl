{extends file="parent:frontend/account/profile.tpl"}

{block name='frontend_account_profile_profile_input_firstname'}
    {$smarty.block.parent}
    {if $endereco_is_active}
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
{/block}
