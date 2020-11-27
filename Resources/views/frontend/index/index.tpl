{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_header_javascript"}
    {$smarty.block.parent}
    <script async defer src="{link file="frontend/_public/src/js/endereco.min.js"}?ver={$endereco_plugin_version}"></script>
    <script>
        ( function() {
            var $interval = setInterval( function() {
                if (window.EnderecoIntegrator && window.EnderecoIntegrator.loaded) {
                    window.EnderecoIntegrator.defaultCountry = (!!('{config name='defaultCountry' namespace="EnderecoShopware5Client"}'))?'{config name='defaultCountry' namespace="EnderecoShopware5Client"}':"de";
                    window.EnderecoIntegrator.defaultCountrySelect = !!('{config name='defaultCountryActive' namespace="EnderecoShopware5Client"}');
                    window.EnderecoIntegrator.config.apiUrl = '{link file="frontend/_public/io.php"}';
                    window.EnderecoIntegrator.config.splitStreet = !!('{config name='splitStreet' namespace="EnderecoShopware5Client"}');
                    window.EnderecoIntegrator.config.apiKey = '{config name='apiKey' namespace="EnderecoShopware5Client"}';
                    window.EnderecoIntegrator.config.showDebugInfo = !!('{config name='showDebug' namespace="EnderecoShopware5Client"}');
                    window.EnderecoIntegrator.config.remoteApiUrl = '{config name='remoteApiUrl' namespace="EnderecoShopware5Client"}';
                    window.EnderecoIntegrator.config.trigger.onblur = !!('{config name='checkOnBlur' namespace="EnderecoShopware5Client"}');
                    window.EnderecoIntegrator.config.trigger.onsubmit = !!('{config name='checkOnSubmit' namespace="EnderecoShopware5Client"}');
                    window.EnderecoIntegrator.config.ux.smartFill = !!('{config name='smartFill' namespace="EnderecoShopware5Client"}');
                    window.EnderecoIntegrator.config.ux.checkExisting = !!('{config name='checkExisting' namespace="EnderecoShopware5Client"}');
                    window.EnderecoIntegrator.config.ux.resumeSubmit = !!('{config name='resumeSubmit' namespace="EnderecoShopware5Client"}');
                    window.EnderecoIntegrator.config.ux.showEmailStatus = !!('{config name='showEmailStatus' namespace="EnderecoShopware5Client"}');
                    window.EnderecoIntegrator.countryMappingUrl = '{url controller='EnderecoShopware5Client' action='country' _seo=false}';
                    window.EnderecoIntegrator.config.templates.button = '<button class="btn is--primary address--form-submit is--large" type="button" endereco-use-selection>{s namespace='EnderecoShopware5Client' name='useSelected'}{/s}</button>';
                    window.EnderecoIntegrator.config.texts = {
                        popUpHeadline: "{s namespace='EnderecoShopware5Client' name='popUpHeadline'}{/s}",
                        popUpSubline: "{s namespace='EnderecoShopware5Client' name='popUpSubline'}{/s}",
                        yourInput: "{s namespace='EnderecoShopware5Client' name='yourInput'}{/s}",
                        editYourInput: "{s namespace='EnderecoShopware5Client' name='editYourInput'}{/s}",
                        ourSuggestions: "{s namespace='EnderecoShopware5Client' name='ourSuggestions'}{/s}",
                        useSelected: "{s namespace='EnderecoShopware5Client' name='useSelected'}{/s}",
                        popupHeadlines: {
                            general_address: "{s namespace='EnderecoShopware5Client' name='popUpHeadline'}{/s}",
                            billing_address: "{s namespace='EnderecoShopware5Client' name='popUpHeadlineBilling'}{/s}",
                            shipping_address: "{s namespace='EnderecoShopware5Client' name='popUpHeadlineShipping'}{/s}",
                        },
                        statuses: {
                            'email_not_correct': "{s namespace='EnderecoShopware5Client' name='statusEmailNotCorrect'}{/s}",
                            'email_cant_receive': "{s namespace='EnderecoShopware5Client' name='statusEmailCantReceive'}{/s}",
                            'email_syntax_error': "{s namespace='EnderecoShopware5Client' name='statusEmailSyntaxError'}{/s}",
                        }
                    };
                    window.EnderecoIntegrator.activeServices = {
                        ams: !!('{config name='amsActive' namespace="EnderecoShopware5Client"}'),
                        emailService: !!('{config name='emailCheckActive' namespace="EnderecoShopware5Client"}'),
                        personService: !!('{config name='salutationCheckActive' namespace="EnderecoShopware5Client"}')
                    }
                    window.EnderecoIntegrator.ready = true;
                    clearInterval($interval);
                }
            }, 100);
        })();
    </script>
{/block}
