{extends file="parent:frontend/register/index.tpl"}

{block name='frontend_register_billing_fieldset_input_country'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_billing_fieldset_input_country'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_billing_fieldset_input_country_states'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_billing_fieldset_input_country_states'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_billing_fieldset_input_zip_and_city'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_billing_fieldset_input_zip_and_city'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_billing_fieldset_different_shipping'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_billing_fieldset_different_shipping'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_billing_fieldset_input_street'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_billing_fieldset_input_street'}
            {$smarty.block.parent}
            {if $endereco_split_street && $endereco_ams_is_active}
                <style>
					.register--street:not(.mopt-wunschpaket-streetwrapper),
					div.address--street:not(.mopt-wunschpaket-streetwrapper) {
						display: none !important;
					}

					.register--content .register--field-streetname {
						float: left;
						width: 78%;
						margin-right: 2%;
					}

					.register--content .register--field-streetnumber {
						float: left;
						width: 20%;
					}

					.endereco-hide-fields {
						display: none !important;
					}

					.register--street-name-number,
					.register--zip-city {
						overflow: auto;
						width: 100%;
					}
                </style>
                <div class="register--street-name-number">
                    <input autocomplete="section-billing billing street-address-name"
                           name="register[billing][attribute][enderecostreetname]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterPlaceholderStreetName' namespace='EnderecoShopware5Client'}Straße{/s}{s name='RequiredField' namespace='frontend/register/index'}{/s}"
                           id="billing_streetname"
                           value="{$form_data.attribute.enderecostreetname|escape}"
                           class="register--field register--spacer register--field-streetname register--field-city is--required{if isset($error_flags.street)} has--error{/if}" />
                    <input autocomplete="section-billing billing street-address-number"
                           name="register[billing][attribute][enderecobuildingnumber]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterPlaceholderStreetNumber' namespace='EnderecoShopware5Client'}Hausnummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="billing_streetnumber"
                           value="{$form_data.attribute.enderecobuildingnumber|escape}"
                           class="register--field register--field-streetnumber address--field-zipcode is--required" />
                </div>
            {else}
                <input type="hidden" name="register[billing][attribute][enderecostreetname]" value="{$form_data.attribute.enderecostreetname|escape}" />
                <input type="hidden" name="register[billing][attribute][enderecobuildingnumber]" value="{$form_data.attribute.enderecobuildingnumber|escape}" />
            {/if}
            <input type="hidden" name="register[billing][attribute][enderecoamsstatus]" value="{$form_data.attribute.enderecoamsstatus|escape}" />
            <input type="hidden" name="register[billing][attribute][enderecoamsts]" value="{$form_data.attribute.enderecoamsts|escape}" />
            <input type="hidden" name="register[billing][attribute][enderecoamsapredictions]" value="{$form_data.attribute.enderecoamsapredictions|escape}" />
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_billing_fieldset_input_addition_address_line1'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_billing_fieldset_input_addition_address_line1'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_billing_fieldset_input_addition_address_line2'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_billing_fieldset_input_addition_address_line2'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name='frontend_register_billing_fieldset_body'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        <div hidden="true">
            {$smarty.block.parent}
        </div>

        <div class="panel--body is--wide">
            {$smarty.capture.c_frontend_register_billing_fieldset_input_country}
            {$smarty.capture.c_frontend_register_billing_fieldset_input_country_states}
            {$smarty.capture.c_frontend_register_billing_fieldset_input_zip_and_city}
            {$smarty.capture.c_frontend_register_billing_fieldset_input_street}
            {$smarty.capture.c_frontend_register_billing_fieldset_input_addition_address_line1}
            {$smarty.capture.c_frontend_register_billing_fieldset_input_addition_address_line2}
            {$smarty.capture.c_frontend_register_billing_fieldset_different_shipping}
        </div>
        <script>
            ( function() {
                var $interval = setInterval( function() {
                    if (window.EnderecoIntegrator && window.EnderecoIntegrator.ready) {
                        window.EnderecoIntegrator.initAMS(
                            'register[billing]',
                            {
                                name: 'billing',
                                addressType: 'billing_address'
                            }
                        );
                        clearInterval($interval);
                    }
                }, 100);
            })();
        </script>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}


{block name='frontend_register_shipping_fieldset_input_salutation'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_shipping_fieldset_input_salutation'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_shipping_fieldset_input_firstname'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_shipping_fieldset_input_firstname'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_shipping_fieldset_input_lastname'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_shipping_fieldset_input_lastname'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_shipping_fieldset_input_country'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_shipping_fieldset_input_country'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_shipping_fieldset_input_country_states'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_shipping_fieldset_input_country_states'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_shipping_fieldset_input_zip_and_city'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_shipping_fieldset_input_zip_and_city'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_shipping_fieldset_different_shipping'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_shipping_fieldset_different_shipping'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_shipping_fieldset_input_street'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_shipping_fieldset_input_street'}
            {$smarty.block.parent}

            {if $endereco_split_street && $endereco_ams_is_active}
                <style>
					.register--street:not(.mopt-wunschpaket-streetwrapper),
					div.address--street:not(.mopt-wunschpaket-streetwrapper) {
						display: none !important;
					}

					.register--content .register--field-streetname {
						float: left;
						width: 78%;
						margin-right: 2%;
					}

					.register--content .register--field-streetnumber {
						float: left;
						width: 20%;
					}

					.endereco-hide-fields {
						display: none !important;
					}

					.register--street-name-number,
					.register--zip-city {
						overflow: auto;
						width: 100%;
					}
                </style>

                <div class="register--street-name-number">
                    <input autocomplete="section-shipping shipping street-address-name"
                           name="register[shipping][attribute][enderecostreetname]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterPlaceholderStreetName' namespace='EnderecoShopware5Client'}Street{/s}{s name='RequiredField' namespace='frontend/register/index'}{/s}"
                           id="shipping_streetname"
                           value="{$form_data.attribute.enderecostreetname|escape}"
                           class="register--field register--spacer register--field-streetname register--field-city is--required{if isset($error_flags.street)} has--error{/if}" />
                    <input autocomplete="section-shipping shipping street-address-number"
                           name="register[shipping][attribute][enderecobuildingnumber]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterPlaceholderStreetNumber' namespace='EnderecoShopware5Client'}Number{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="shipping_streetnumber"
                           value="{$form_data.attribute.enderecobuildingnumber|escape}"
                           class="register--field register--field-streetnumber address--field-zipcode is--required" />
                </div>
            {else}
                <input type="hidden" name="register[shipping][attribute][enderecostreetname]" value="{$form_data.attribute.enderecostreetname|escape}" />
                <input type="hidden" name="register[shipping][attribute][enderecobuildingnumber]" value="{$form_data.attribute.enderecobuildingnumber|escape}" />
            {/if}
            <input type="hidden" name="register[shipping][attribute][enderecoamsstatus]" value="{$form_data.attribute.enderecoamsstatus|escape}" />
            <input type="hidden" name="register[shipping][attribute][enderecoamsts]" value="{$form_data.attribute.enderecoamsts|escape}" />
            <input type="hidden" name="register[shipping][attribute][enderecoamsapredictions]" value="{$form_data.attribute.enderecoamsapredictions|escape}" />
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_shipping_fieldset_input_addition_address_line1'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_shipping_fieldset_input_addition_address_line1'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_shipping_fieldset_input_addition_address_line2'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {capture name='c_frontend_register_shipping_fieldset_input_addition_address_line2'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name='frontend_register_shipping_fieldset_body'}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        <div>
            {$smarty.block.parent}
        </div>
        <div class="panel--body is--wide" style="padding-top:0">
            {$smarty.capture.c_frontend_register_shipping_fieldset_input_salutation}
            {$smarty.capture.c_frontend_register_shipping_fieldset_input_firstname}
            {$smarty.capture.c_frontend_register_shipping_fieldset_input_lastname}
            {$smarty.capture.c_frontend_register_shipping_fieldset_input_country}
            {$smarty.capture.c_frontend_register_shipping_fieldset_input_country_states}
            {$smarty.capture.c_frontend_register_shipping_fieldset_input_zip_and_city}
            {$smarty.capture.c_frontend_register_shipping_fieldset_input_street}
            {$smarty.capture.c_frontend_register_shipping_fieldset_input_addition_address_line1}
            {$smarty.capture.c_frontend_register_shipping_fieldset_input_addition_address_line2}
            {$smarty.capture.c_frontend_register_shipping_fieldset_different_shipping}
        </div>
        <script>
            ( function() {
                var $interval = setInterval( function() {
                    if (window.EnderecoIntegrator && window.EnderecoIntegrator.ready) {
                        window.EnderecoIntegrator.initAMS(
                            'register[shipping]',
                            {
                                name: 'shipping',
                                addressType: 'shipping_address'
                            }
                        );
                        clearInterval($interval);
                    }
                }, 100);
            })();
        </script>
        <script>
            (function() {
                var $prevValue = '';
                function handleSelect(wunschSelector) {
                    var streetNameBlock = wunschSelector.form.querySelector('.register--shipping .register--street-name-number');
                    var streetNamefullBlock = wunschSelector.form.querySelector('.register--shipping .register--street');

                    if ($prevValue !== wunschSelector.value) {
                        $prevValue = wunschSelector.value;
                        if ('address' === wunschSelector.value) {
                            if (streetNameBlock) {
                                streetNameBlock.classList.remove('endereco-hide-fields');
                                streetNameBlock.querySelector('[name="register[shipping][attribute][enderecostreetname]"]').required = true;
                                streetNameBlock.querySelector('[name="register[shipping][attribute][enderecobuildingnumber]"]').required = false;
                            }
                            if (streetNamefullBlock) {
                                streetNamefullBlock.classList.add('endereco-hide-fields');
                            }
                            var interval = setInterval( function() {
                                if (undefined !== EnderecoIntegrator.integratedObjects.shipping_ams) {
                                    EnderecoIntegrator.integratedObjects.shipping_ams.addressType = 'shipping_address';
                                    clearInterval(interval);
                                }
                            }, 100);
                        } else {
                            if (streetNameBlock) {
                                streetNameBlock.classList.add('endereco-hide-fields');
                                if (window.EnderecoIntegrator.integratedObjects.shipping_ams) {
                                    window.EnderecoIntegrator.integratedObjects.shipping_ams.addressStatus = [""];
                                }
                                streetNameBlock.querySelector('[name="register[shipping][attribute][enderecostreetname]"]').required = false;
                                streetNameBlock.querySelector('[name="register[shipping][attribute][enderecobuildingnumber]"]').required = false;
                            }
                            if (streetNamefullBlock) {
                                streetNamefullBlock.classList.remove('endereco-hide-fields');
                            }
                            var interval = setInterval( function() {
                                if (undefined !== EnderecoIntegrator.integratedObjects.shipping_ams) {
                                    EnderecoIntegrator.integratedObjects.shipping_ams.addressType = wunschSelector.value;
                                    clearInterval(interval)
                                }
                            }, 100);
                        }
                    }
                }

                var $waitForcomplete = setInterval( function() {
                    if (document.readyState === "complete") {
                        var wunschSelector = document.querySelector('[name="register[shipping][attribute][moptwunschpaketaddresstype]"]');
                        if (wunschSelector) {
                            var $origValue = wunschSelector.value;
                            handleSelect(wunschSelector);
                            var $checkWunschselector = setInterval(function() {
                                if (!!wunschSelector && $origValue !== wunschSelector.value) {
                                    $origValue = wunschSelector.value;
                                    handleSelect(wunschSelector);
                                }
                            }, 100);
                        }
                    }
                }, 100);

            })();
        </script>

        <script>
            (function() {
                function addRequiredToggler(DOMElement) {
                    if (DOMElement) {
                        var tempInterval = setInterval( function() {
                            if (DOMElement) {

                                // Is visible and has "is--required" class?
                                if (
                                    DOMElement.classList.contains('is--required') &&
                                    !isHidden(DOMElement)
                                ) {
                                    if (!DOMElement.required) {
                                        DOMElement.required = true;
                                    }
                                }  else {
                                    DOMElement.required = false;
                                }
                            } else {
                                clearInterval(tempInterval)
                            }
                        }, 100);
                    }
                }

                function isHidden(DOMElement) {
                    return (DOMElement.offsetParent === null)
                }


                var waitForComplete = setInterval(function() {
                    if ('complete' === document.readyState) {

                        // Add advanced required toggler to street inputs.
                        addRequiredToggler(document.querySelector('[name="register[shipping][street]"]'));
                        addRequiredToggler(document.querySelector('[name="register[shipping][attribute][enderecostreetname]"]'));
                        addRequiredToggler(document.querySelector('[name="register[shipping][attribute][enderecobuildingnumber]"]'));

                        addRequiredToggler(document.querySelector('[name="register[billing][street]"]'));
                        addRequiredToggler(document.querySelector('[name="register[billing][attribute][enderecostreetname]"]'));
                        addRequiredToggler(document.querySelector('[name="register[billing][attribute][enderecobuildingnumber]"]'));

                        clearInterval(waitForComplete);
                    }
                }, 100);
            })();
        </script>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

