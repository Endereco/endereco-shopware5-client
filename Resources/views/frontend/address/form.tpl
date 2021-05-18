{extends file="parent:frontend/address/form.tpl"}

{block name='frontend_address_form_input_salutation'}
    {if $endereco_is_active}
        {capture name='c_frontend_address_form_input_salutation'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_register_personal_fieldset_input_title'}
    {if $endereco_is_active}
        {capture name='c_frontend_register_personal_fieldset_input_title'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}

{/block}
{block name='frontend_address_form_input_firstname'}
    {if $endereco_is_active}
        {capture name='c_frontend_address_form_input_firstname'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_address_form_input_lastname'}
    {if $endereco_is_active}
        {capture name='c_frontend_address_form_input_lastname'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_address_form_input_street'}
    {if $endereco_is_active}
        {capture name='c_frontend_address_form_input_street'}
            {$smarty.block.parent}


            <style>
				.address--street.mopt-wunschpaket-streetwrapper {
					width: 100%;
					overflow: auto;
				}
				.address--street-name-number,
				.address--zip-city {
					overflow: auto;
					width: 100%;
				}
            </style>

            {if $endereco_split_street}
                <style>
					.register--street:not(.mopt-wunschpaket-streetwrapper),
					div.address--street:not(.mopt-wunschpaket-streetwrapper) {
						display: none !important;
					}

					.address--street-name-number .address--field-streetname {
						float: left;
						width: 78%;
						margin-right: 2%;
					}

					.address--street-name-number .address--field-streetnumber {
						float: left;
						width: 20%;
					}

					.endereco-hide-fields {
						display: none !important;
					}
                </style>
                <input type="hidden" name="address_form_prefix" value="{$inputPrefix}"/>
                <div class="address--zip-city address--street-name-number">
                    <input autocomplete="section-billing billing street-address"
                           name="{$inputPrefix}[attribute][enderecostreetname]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterPlaceholderStreetName' namespace='EnderecoShopware5Client'}Straße{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="address_streetname"
                           value="{$formData.attribute.enderecostreetname}"
                           class="address--field address--spacer address--field-streetname address--field-city is--required{if $error_flags.street} has--error{/if}"/>
                    <input autocomplete="section-billing billing street-address"
                           name="{$inputPrefix}[attribute][enderecobuildingnumber]"
                           type="text"
                           required="required"
                           aria-required="true"
                           placeholder="{s name='RegisterPlaceholderStreetNumber' namespace='EnderecoShopware5Client'}Hausnummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                           id="address_streetnumber"
                           value="{$formData.attribute.enderecobuildingnumber}"
                           class="address--field address--field-streetnumber address--field-zipcode is--required"/>
                </div>
            {/if}

            <input type="hidden" name="{$inputPrefix}[attribute][enderecoamsstatus]" value="{$formData.attribute.enderecoamsstatus|escape}" />
            <input type="hidden" name="{$inputPrefix}[attribute][enderecoamsts]" value="{$formData.attribute.enderecoamsts|escape}" />
            <input type="hidden" name="{$inputPrefix}[attribute][enderecoamsapredictions]" value="{$formData.attribute.enderecoamsapredictions|escape}" />
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_address_form_input_addition_address_line1'}
    {if $endereco_is_active}
        {capture name='c_frontend_address_form_input_addition_address_line1'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_address_form_input_addition_address_line2'}
    {if $endereco_is_active}
        {capture name='c_frontend_address_form_input_addition_address_line2'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_address_form_input_zip_and_city'}
    {if $endereco_is_active}
        {capture name='c_frontend_address_form_input_zip_and_city'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_address_form_input_country'}
    {if $endereco_is_active}
        {capture name='c_frontend_address_form_input_country'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_address_form_input_country_states'}
    {if $endereco_is_active}
        {capture name='c_frontend_address_form_input_country_states'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_address_form_input_phone'}
    {if $endereco_is_active}
        {capture name='c_frontend_address_form_input_phone'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_address_form_input_set_default_shipping'}
    {if $endereco_is_active}
        {capture name='c_frontend_address_form_input_set_default_shipping'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name='frontend_address_form_input_set_default_billing'}
    {if $endereco_is_active}
        {capture name='c_frontend_address_form_input_set_default_billing'}
            {$smarty.block.parent}
        {/capture}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name='frontend_address_form_fieldset_address'}
    {$smarty.block.parent}
    {if $endereco_is_active}
        {$smarty.capture.c_frontend_address_form_input_salutation}
        {$smarty.capture.c_frontend_register_personal_fieldset_input_title}
        {$smarty.capture.c_frontend_address_form_input_firstname}
        {$smarty.capture.c_frontend_address_form_input_lastname}
        {$smarty.capture.c_frontend_address_form_input_country}
        {$smarty.capture.c_frontend_address_form_input_country_states}
        {$smarty.capture.c_frontend_address_form_input_zip_and_city}
        {$smarty.capture.c_frontend_address_form_input_street}
        {$smarty.capture.c_frontend_address_form_input_addition_address_line1}
        {$smarty.capture.c_frontend_address_form_input_addition_address_line2}
        {$smarty.capture.c_frontend_address_form_input_phone}
        {$smarty.capture.c_frontend_address_form_input_set_default_shipping}
        {$smarty.capture.c_frontend_address_form_input_set_default_billing}

        <script>
            ( function() {
                var $interval = setInterval( function() {
                    if (window.EnderecoIntegrator && window.EnderecoIntegrator.ready) {
                        window.EnderecoIntegrator.initAMS(
                            '{$inputPrefix}',
                            {
                                name: {if !$formData.id || $sUserData.additional.user.default_billing_address_id != $formData.id}'shipping'{elseif $sUserData.additional.user.default_billing_address_id == $formData.id}'billing'{else}'general'{/if},
                                addressType: {if !$formData.id || $sUserData.additional.user.default_billing_address_id != $formData.id}'shipping_address'{elseif $sUserData.additional.user.default_billing_address_id == $formData.id}'billing_address'{else}'general_address'{/if}
                            }
                        );
                        window.EnderecoIntegrator.initPersonServices('{$inputPrefix}');
                        clearInterval($interval);
                    }
                }, 100);
            })();
        </script>

        <script>
            (function() {
                var $prevValue = '';
                function handleSelect(wunschSelector) {
                    var streetNameBlock = wunschSelector.form.querySelector('.address--street-name-number');
                    var streetNamefullBlock = wunschSelector.form.querySelector('.address--street');

                    if ($prevValue !== wunschSelector.value) {
                        $prevValue = wunschSelector.value;
                        if ('address' === wunschSelector.value) {
                            if (streetNameBlock) {
                                streetNameBlock.classList.remove('endereco-hide-fields');
                                streetNameBlock.querySelector('[name="{$inputPrefix}[streetname]"]').required = true;
                                streetNameBlock.querySelector('[name="{$inputPrefix}[streetnumber]"]').required = false;
                            }
                            if (streetNamefullBlock) {
                                streetNamefullBlock.classList.add('endereco-hide-fields');
                            }
                            var interval = setInterval( function() {
                                if (!!EnderecoIntegrator.integratedObjects.shipping_ams && EnderecoIntegrator.integratedObjects.shipping_ams.active) {
                                    EnderecoIntegrator.integratedObjects.shipping_ams.addressType = 'shipping_address';
                                    clearInterval(interval);
                                }
                            }, 100);
                        } else {
                            if (streetNameBlock) {
                                streetNameBlock.classList.add('endereco-hide-fields');
                                streetNameBlock.querySelector('[name="{$inputPrefix}[streetname]"]').required = false;
                                streetNameBlock.querySelector('[name="{$inputPrefix}[streetnumber]"]').required = false;
                            }
                            if (streetNamefullBlock) {
                                streetNamefullBlock.classList.remove('endereco-hide-fields');
                            }
                            var interval = setInterval( function() {
                                if (!!EnderecoIntegrator.integratedObjects.shipping_ams && EnderecoIntegrator.integratedObjects.shipping_ams.active) {
                                    EnderecoIntegrator.integratedObjects.shipping_ams.addressType = wunschSelector.value;
                                    clearInterval(interval)
                                }
                            }, 100);
                        }
                    }
                }

                var $waitForcomplete = setInterval( function() {
                    if (document.readyState === "complete") {
                        var wunschSelector = document.querySelector('[name="{$inputPrefix}[attribute][moptwunschpaketaddresstype]"]');
                        var streetNamefullBlock = document.querySelector('div.address--street');

                        if (!wunschSelector && streetNamefullBlock && streetNamefullBlock.classList.contains('mopt-wunschpaket-streetwrapper')) {
                            if (streetNamefullBlock) {
                                streetNamefullBlock.classList.add('endereco-hide-fields');
                            }
                        }

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
    {/if}
{/block}
