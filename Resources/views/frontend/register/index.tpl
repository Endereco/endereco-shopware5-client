{extends file="parent:frontend/register/index.tpl"}

{block name='frontend_register_billing_fieldset_input_country'}
    {capture name='c_frontend_register_billing_fieldset_input_country'}
        {$smarty.block.parent}
    {/capture}
{/block}
{block name='frontend_register_billing_fieldset_input_country_states'}
    {capture name='c_frontend_register_billing_fieldset_input_country_states'}
        {$smarty.block.parent}
    {/capture}
{/block}
{block name='frontend_register_billing_fieldset_input_zip_and_city'}
    {capture name='c_frontend_register_billing_fieldset_input_zip_and_city'}
        {$smarty.block.parent}
    {/capture}
{/block}
{block name='frontend_register_billing_fieldset_different_shipping'}
    {capture name='c_frontend_register_billing_fieldset_different_shipping'}
        {$smarty.block.parent}
    {/capture}
{/block}
{block name='frontend_register_billing_fieldset_input_street'}
    {capture name='c_frontend_register_billing_fieldset_input_street'}
        {$smarty.block.parent}
        {if $endereco_split_street}
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
                       name="register[billing][streetname]"
                       type="text"
                       required="required"
                       aria-required="true"
                       placeholder="{s name='RegisterPlaceholderStreetName' namespace='EnderecoShopware5Client'}Straße{/s}{s name='RequiredField' namespace='frontend/register/index'}{/s}"
                       id="billing_streetname"
                       value=""
                       class="register--field register--spacer register--field-streetname register--field-city is--required{if isset($error_flags.street)} has--error{/if}" />
                <input autocomplete="section-billing billing street-address-number"
                       name="register[billing][streetnumber]"
                       type="text"
                       required="required"
                       aria-required="true"
                       placeholder="{s name='RegisterPlaceholderStreetNumber' namespace='EnderecoShopware5Client'}Hausnummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                       id="billing_streetnumber"
                       value=""
                       class="register--field register--field-streetnumber address--field-zipcode is--required" />
            </div>
        {/if}
        <input type="hidden" name="register[billing][attribute][enderecoamsstatus]" value="{$form_data.attribute.enderecoamsstatus|escape}" />
        <input type="hidden" name="register[billing][attribute][enderecoamsts]" value="{$form_data.attribute.enderecoamsts|escape}" />
        <input type="hidden" name="register[billing][attribute][enderecoamsapredictions]" value="{$form_data.attribute.enderecoamsapredictions|escape}" />
    {/capture}
{/block}
{block name='frontend_register_billing_fieldset_input_addition_address_line1'}
    {capture name='c_frontend_register_billing_fieldset_input_addition_address_line1'}
        {$smarty.block.parent}
    {/capture}
{/block}
{block name='frontend_register_billing_fieldset_input_addition_address_line2'}
    {capture name='c_frontend_register_billing_fieldset_input_addition_address_line2'}
        {$smarty.block.parent}
    {/capture}
{/block}

{block name='frontend_register_billing_fieldset_body'}
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
                            addressType: 'billing_address'
                        }
                    );
                    clearInterval($interval);
                }
            }, 100);
        })();
    </script>
{/block}


{block name='frontend_register_shipping_fieldset_input_salutation'}
    {capture name='c_frontend_register_shipping_fieldset_input_salutation'}
        {$smarty.block.parent}
    {/capture}
{/block}
{block name='frontend_register_shipping_fieldset_input_firstname'}
    {capture name='c_frontend_register_shipping_fieldset_input_firstname'}
        {$smarty.block.parent}
    {/capture}
{/block}
{block name='frontend_register_shipping_fieldset_input_lastname'}
    {capture name='c_frontend_register_shipping_fieldset_input_lastname'}
        {$smarty.block.parent}
    {/capture}
{/block}
{block name='frontend_register_shipping_fieldset_input_country'}
    {capture name='c_frontend_register_shipping_fieldset_input_country'}
        {$smarty.block.parent}
    {/capture}
{/block}
{block name='frontend_register_shipping_fieldset_input_country_states'}
    {capture name='c_frontend_register_shipping_fieldset_input_country_states'}
        {$smarty.block.parent}
    {/capture}
{/block}
{block name='frontend_register_shipping_fieldset_input_zip_and_city'}
    {capture name='c_frontend_register_shipping_fieldset_input_zip_and_city'}
        {$smarty.block.parent}
    {/capture}
{/block}
{block name='frontend_register_shipping_fieldset_different_shipping'}
    {capture name='c_frontend_register_shipping_fieldset_different_shipping'}
        {$smarty.block.parent}
    {/capture}
{/block}
{block name='frontend_register_shipping_fieldset_input_street'}
    {capture name='c_frontend_register_shipping_fieldset_input_street'}
        {$smarty.block.parent}

        {if $endereco_split_street}
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
                       name="register[shipping][streetname]"
                       type="text"
                       required="required"
                       aria-required="true"
                       placeholder="{s name='RegisterPlaceholderStreetName' namespace='EnderecoShopware5Client'}Street{/s}{s name='RequiredField' namespace='frontend/register/index'}{/s}"
                       id="shipping_streetname"
                       value=""
                       class="register--field register--spacer register--field-streetname register--field-city is--required{if isset($error_flags.street)} has--error{/if}" />
                <input autocomplete="section-shipping shipping street-address-number"
                       name="register[shipping][streetnumber]"
                       type="text"
                       required="required"
                       aria-required="true"
                       placeholder="{s name='RegisterPlaceholderStreetNumber' namespace='EnderecoShopware5Client'}Number{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                       id="shipping_streetnumber"
                       value=""
                       class="register--field register--field-streetnumber address--field-zipcode is--required" />
            </div>
        {/if}
        <input type="hidden" name="register[shipping][attribute][enderecoamsstatus]" value="{$form_data.attribute.enderecoamsstatus|escape}" />
        <input type="hidden" name="register[shipping][attribute][enderecoamsts]" value="{$form_data.attribute.enderecoamsts|escape}" />
        <input type="hidden" name="register[shipping][attribute][enderecoamsapredictions]" value="{$form_data.attribute.enderecoamsapredictions|escape}" />
    {/capture}
{/block}
{block name='frontend_register_shipping_fieldset_input_addition_address_line1'}
    {capture name='c_frontend_register_shipping_fieldset_input_addition_address_line1'}
        {$smarty.block.parent}
    {/capture}
{/block}
{block name='frontend_register_shipping_fieldset_input_addition_address_line2'}
    {capture name='c_frontend_register_shipping_fieldset_input_addition_address_line2'}
        {$smarty.block.parent}
    {/capture}
{/block}

{block name='frontend_register_shipping_fieldset_body'}
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
                            streetNameBlock.querySelector('[name="register[shipping][streetname]"]').required = true;
                            streetNameBlock.querySelector('[name="register[shipping][streetnumber]"]').required = false;
                        }
                        if (streetNamefullBlock) {
                            streetNamefullBlock.classList.add('endereco-hide-fields');
                        }
                        var interval = setInterval( function() {
                            if (undefined !== EnderecoIntegrator.integratedObjects.register_shipping__ams) {
                                EnderecoIntegrator.integratedObjects.register_shipping__ams.addressType = 'shipping_address';
                                clearInterval(interval);
                            }
                        }, 100);
                    } else {
                        if (streetNameBlock) {
                            streetNameBlock.classList.add('endereco-hide-fields');
                            if (window.EnderecoIntegrator.integratedObjects.register_shipping__ams) {
                                window.EnderecoIntegrator.integratedObjects.register_shipping__ams.addressStatus = [""];
                            }
                            streetNameBlock.querySelector('[name="register[shipping][streetname]"]').required = false;
                            streetNameBlock.querySelector('[name="register[shipping][streetnumber]"]').required = false;
                        }
                        if (streetNamefullBlock) {
                            streetNamefullBlock.classList.remove('endereco-hide-fields');
                        }
                        var interval = setInterval( function() {
                            if (undefined !== EnderecoIntegrator.integratedObjects.register_shipping__ams) {
                                EnderecoIntegrator.integratedObjects.register_shipping__ams.addressType = wunschSelector.value;
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
                    addRequiredToggler(document.querySelector('[name="register[shipping][streetname]"]'));
                    addRequiredToggler(document.querySelector('[name="register[shipping][streetnumber]"]'));

                    clearInterval(waitForComplete);
                }
            }, 100);
        })();
    </script>
{/block}

