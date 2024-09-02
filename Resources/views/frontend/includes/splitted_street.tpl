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
<style>
    .address--street-name-number .address--field-streetname {
        float: left;
        width: 78%;
        margin-right: 2%;
    }

    .address--street-name-number .address--field-streetnumber {
        float: left;
        width: 20%;
    }

    .address--street-name-number,
    .address--zip-city {
        overflow: auto;
        width: 100%;
    }
</style>