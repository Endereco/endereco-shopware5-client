{extends file="parent:frontend/index/header.tpl"}

{block name="frontend_index_header_css_screen"}
    {$smarty.block.parent}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {if $endereco_main_color}
            <style>
				.endereco-predictions-wrapper .endereco-span--neutral {
					border-bottom: 1px dotted {$endereco_main_color} !important;
					color: {$endereco_main_color} !important;
				}
				.endereco-predictions .endereco-predictions__item.endereco-predictions__item.endereco-predictions__item:hover,
				.endereco-predictions .endereco-predictions__item.endereco-predictions__item.endereco-predictions__item.active {
					background-color: {$endereco_main_color_bg} !important;
				}
            </style>
        {/if}

        {if $endereco_error_color}
            <style>
				.endereco-modal__header-main {
					color: {$endereco_error_color} !important;
				}

				.endereco-address-predictions--original .endereco-address-predictions__label {
					border-color: {$endereco_error_color} !important;
				}

				.endereco-address-predictions--original .endereco-span--remove {
					background-color: {$endereco_error_color_bg} !important;
					border-bottom: 1px solid {$endereco_error_color_bg} !important;
				}

            </style>
        {/if}

        {if $endereco_success_color}
            <style>
				.endereco-address-predictions__radio:checked ~ .endereco-address-predictions__label,
				.endereco-address-predictions__item.active .endereco-address-predictions__label {
					border-color: {$endereco_success_color} !important;
				}

				.endereco-address-predictions__radio:checked ~ .endereco-address-predictions__label::before,
				.endereco-address-predictions__item.active .endereco-address-predictions__label::before {
					border-color: {$endereco_success_color} !important;
				}

				.endereco-address-predictions__label::after {
					background-color: {$endereco_success_color} !important;
				}

				.endereco-address-predictions--suggestions .endereco-span--add {
					border-bottom: 1px solid @successColor;
					background-color: {$endereco_success_color_bg} !important;
				}

            </style>
        {/if}
    {/if}
{/block}

{block name="frontend_index_header_javascript_tracking"}
    {$smarty.block.parent}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        <script>
            ( function() {
                var $interval = setInterval( function() {
                    if (window.EnderecoIntegrator && window.EnderecoIntegrator.loaded) {
                        window.EnderecoIntegrator.defaultCountry = (!!('{config name='defaultCountry' namespace="EnderecoShopware5Client"}'))?'{config name='defaultCountry' namespace="EnderecoShopware5Client"}':"de";
                        window.EnderecoIntegrator.defaultCountrySelect = !!('{config name='defaultCountryActive' namespace="EnderecoShopware5Client"}');
                        window.EnderecoIntegrator.themeName = '{$endereco_theme_name}';
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
                        window.EnderecoIntegrator.config.ux.useStandardCss = !!('{config name='useDefaultCss' namespace="EnderecoShopware5Client"}');
                        window.EnderecoIntegrator.config.ux.allowCloseModal = !!('{config name='allowCloseModal' namespace="EnderecoShopware5Client"}');
                        window.EnderecoIntegrator.config.ux.confirmWithCheckbox = !!('{config name='confirmWithCheckbox' namespace="EnderecoShopware5Client"}');
                        window.EnderecoIntegrator.config.ux.changeFieldsOrder = false;
                        window.EnderecoIntegrator.countryMappingUrl = '{url controller='EnderecoShopware5Client' action='country' _seo=false}';
                        window.EnderecoIntegrator.config.templates.primaryButtonClasses = 'btn is--primary is--large';
                        window.EnderecoIntegrator.config.templates.secondaryButtonClasses = 'btn is--secondary is--large';
                        window.EnderecoIntegrator.config.texts = {
                            "popUpHeadline": "{s namespace='EnderecoShopware5Client' name='popUpHeadline'}{/s}",
                            "popUpSubline": "{s namespace='EnderecoShopware5Client' name='popUpSubline'}{/s}",
                            "mistakeNoPredictionSubline": "{s namespace='EnderecoShopware5Client' name='mistakeNoPredictionSubline'}{/s}",
                            "notFoundSubline": "{s namespace='EnderecoShopware5Client' name='notFoundSubline'}{/s}",
                            "confirmMyAddressCheckbox": "{s namespace='EnderecoShopware5Client' name='confirmMyAddressCheckbox'}{/s}",
                            "yourInput": "{s namespace='EnderecoShopware5Client' name='yourInput'}{/s}",
                            "editYourInput": "{s namespace='EnderecoShopware5Client' name='editYourInput'}{/s}",
                            "ourSuggestions": "{s namespace='EnderecoShopware5Client' name='ourSuggestions'}{/s}",
                            "useSelected": "{s namespace='EnderecoShopware5Client' name='useSelected'}{/s}",
                            "confirmAddress": "{s namespace='EnderecoShopware5Client' name='confirmAddress'}{/s}",
                            "editAddress": "{s namespace='EnderecoShopware5Client' name='editAddress'}{/s}",
                            "warningText": "{s namespace='EnderecoShopware5Client' name='warningText'}{/s}",
                            "popupHeadlines": {
                                "general_address": "{s namespace='EnderecoShopware5Client' name='popUpHeadline'}{/s}",
                                "billing_address": "{s namespace='EnderecoShopware5Client' name='popUpHeadlineBilling'}{/s}",
                                "shipping_address": "{s namespace='EnderecoShopware5Client' name='popUpHeadlineShipping'}{/s}",
                            },
                            "statuses": {
                                "email_not_correct": "{s namespace='EnderecoShopware5Client' name='statusEmailNotCorrect'}{/s}",
                                "email_cant_receive": "{s namespace='EnderecoShopware5Client' name='statusEmailCantReceive'}{/s}",
                                "email_syntax_error": "{s namespace='EnderecoShopware5Client' name='statusEmailSyntaxError'}{/s}",
                                "email_no_mx": "{s namespace='EnderecoShopware5Client' name='statusEmailNoMx'}{/s}",
                                "building_number_is_missing": "{s namespace='EnderecoShopware5Client' name='statusAddressBuildingNumberIsMissing'}{/s}",
                                "building_number_not_found": "{s namespace='EnderecoShopware5Client' name='statusAddressBuildingNumberNotFound'}{/s}",
                                "street_name_needs_correction": "{s namespace='EnderecoShopware5Client' name='statusAddressStreetNameNeedsCorrection'}{/s}",
                                "locality_needs_correction": "{s namespace='EnderecoShopware5Client' name='statusAddressLocalityNeedsCorrection'}{/s}",
                                "postal_code_needs_correction": "{s namespace='EnderecoShopware5Client' name='statusAddressPostalCodeNeedsCorrection'}{/s}",
                                "country_code_needs_correction": "{s namespace='EnderecoShopware5Client' name='statusAddressCountryCodeNeedsCorrection'}{/s}",
                            }
                        };
                        window.EnderecoIntegrator.activeServices = {
                            ams: !!('{config name='amsActive' namespace="EnderecoShopware5Client"}'),
                            emailService: !!('{config name='emailCheckActive' namespace="EnderecoShopware5Client"}'),
                            personService: !!('{config name='salutationCheckActive' namespace="EnderecoShopware5Client"}')
                        }
                        window.EnderecoIntegrator.countryCodeToNameMapping = JSON.parse('{$endereco_country_mapping}');
                        window.EnderecoIntegrator.ready = true;
                        clearInterval($interval);
                    }
                }, 100);
            })();
        </script>
    {/if}

{/block}
