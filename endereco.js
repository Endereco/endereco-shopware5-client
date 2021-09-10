import Promise from 'promise-polyfill';
import merge from 'lodash.merge';
import axios from 'axios';
import EnderecoIntegrator from '../js-sdk/modules/integrator'; // Version 1.1.0-rc.5
import css from '../js-sdk/themes/default-theme.scss'
import 'polyfill-array-includes';

if ('NodeList' in window && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = function (callback, thisArg) {
        thisArg = thisArg || window;
        for (var i = 0; i < this.length; i++) {
            callback.call(thisArg, this[i], i, this);
        }
    };
}

if (!window.Promise) {
    window.Promise = Promise;
}

EnderecoIntegrator.postfix = {
    ams: {
        countryCode: '[country]',
        postalCode: '[zipcode]',
        locality: '[city]',
        streetFull: '[street]',
        streetName: '[attribute][enderecostreetname]',
        buildingNumber: '[attribute][enderecobuildingnumber]',
        addressStatus: '[attribute][enderecoamsstatus]',
        addressTimestamp: '[attribute][enderecoamsts]',
        addressPredictions: '[attribute][enderecoamsapredictions]',
        additionalInfo: '[additionalAddressLine2]',
    },
    personServices: {
        salutation: '[salutation]',
        firstName: '[firstname]'
    },
    emailServices: {
        email: '[email]'
    }
};

EnderecoIntegrator.css = css[0][1];
EnderecoIntegrator.resolvers.countryCodeWrite = function (value) {
    return new Promise(function (resolve, reject) {

        var countyCodeEndpoint = EnderecoIntegrator.countryMappingUrl + '?countryCode=' + value;
        new axios.get(countyCodeEndpoint, {
            timeout: 3000
        })
            .then(function (response) {
                resolve(response.data);
            })
            .catch(function (e) {
                resolve(value);
            }).finally(function () {
        });
    });
}
EnderecoIntegrator.resolvers.countryCodeRead = function (value) {
    return new Promise(function (resolve, reject) {
        var countyEndpoint = EnderecoIntegrator.countryMappingUrl + '?countryId=' + value;
        new axios.get(countyEndpoint, {
            timeout: 3000
        })
            .then(function (response) {
                resolve(response.data);
            })
            .catch(function (e) {
                resolve(value);
            }).finally(function () {
        });
    });
}
EnderecoIntegrator.resolvers.salutationWrite = function (value) {
    var mapping = {
        'F': 'ms',
        'M': 'mr'
    };
    return new Promise(function (resolve, reject) {
        resolve(mapping[value]);
    });
}
EnderecoIntegrator.resolvers.salutationRead = function (value) {
    var mapping = {
        'ms': 'F',
        'mr': 'M'
    };
    return new Promise(function (resolve, reject) {
        resolve(mapping[value]);
    });
}

EnderecoIntegrator.afterAMSActivation.push( function(EAO) {

    // Dirty hack for shopware register button.
    if (!!EAO.onCloseModal) {
        EAO.onCloseModal.push(function(AddressObject) {
            if ($('.register--submit') && $('.register--submit').data('plugin_swPreloaderButton')) {
                $('.register--submit').data('plugin_swPreloaderButton').reset();
            }
            if ($('.address--form-submit') && $('.address--form-submit').data('plugin_swPreloaderButton')) {
                $('.address--form-submit').data('plugin_swPreloaderButton').reset();
            }
        });
    }

    if (!!EAO.onAfterAddressCheckSelected) {
        EAO.onAfterAddressCheckSelected.push(function(AddressObject) {
            if ($('.register--submit') && $('.register--submit').data('plugin_swPreloaderButton')) {
                $('.register--submit').data('plugin_swPreloaderButton').reset();
            }
            if ($('.address--form-submit') && $('.address--form-submit').data('plugin_swPreloaderButton')) {
                $('.address--form-submit').data('plugin_swPreloaderButton').reset();
            }
        });
    }

    if (!!EAO.onAfterAddressCheckNoAction) {
        EAO.onAfterAddressCheckNoAction.push(function(AddressObject) {
            if ($('.register--submit') && $('.register--submit').data('plugin_swPreloaderButton')) {
                $('.register--submit').data('plugin_swPreloaderButton').reset();
            }
            if ($('.address--form-submit') && $('.address--form-submit').data('plugin_swPreloaderButton')) {
                $('.address--form-submit').data('plugin_swPreloaderButton').reset();
            }
        });
    }

    if (!!EAO.onConfirmAddress) {
        EAO.onConfirmAddress.push(function(AddressObject) {
            if ($('.register--submit') && $('.register--submit').data('plugin_swPreloaderButton')) {
                $('.register--submit').data('plugin_swPreloaderButton').reset();
            }
            if ($('.address--form-submit') && $('.address--form-submit').data('plugin_swPreloaderButton')) {
                $('.address--form-submit').data('plugin_swPreloaderButton').reset();
            }
        });
    }

    EAO.forms.forEach( function(form) {
        var $actionPanel = form.querySelector('.address--form-actions');
        if (!$actionPanel) {
            return;
        }
        var $actionPanelClone = $actionPanel.cloneNode(true);

        $actionPanel.parentNode.replaceChild($actionPanelClone, $actionPanel);

        $actionPanelClone.querySelectorAll('button').forEach( function(DOMElement) {
            DOMElement.type = "button";
            DOMElement.addEventListener('click', function(e) {
                form.querySelector('input[name=saveAction]').value = this.getAttribute('data-value');
                if (!EAO.util.shouldBeChecked()) {
                    form.dispatchEvent(
                        new EAO.util.CustomEvent(
                            'submit',
                            {
                                'bubbles': true,
                                'cancelable': true
                            }
                        )
                    );
                } else {
                    // First. Block.
                    e.preventDefault();
                    e.stopPropagation();

                    if (window.EnderecoIntegrator && !window.EnderecoIntegrator.submitResume) {
                        window.EnderecoIntegrator.submitResume = function() {
                            if(form.dispatchEvent(
                                new EAO.util.CustomEvent(
                                    'submit',
                                    {
                                        'bubbles': true,
                                        'cancelable': true
                                    }
                                )
                            )) {
                                form.submit();
                            }
                            window.EnderecoIntegrator.submitResume = undefined;
                        }
                    }

                    setTimeout(function() {
                        EAO.util.checkAddress()
                            .catch(function() {
                                EAO.waitForAllPopupsToClose().then(function() {
                                    if (window.EnderecoIntegrator && window.EnderecoIntegrator.submitResume) {
                                        window.EnderecoIntegrator.submitResume();
                                    }
                                }).catch()
                            });
                    }, 300);

                    return false;
                }
            })
        });

        window.StateManager.addPlugin('*[data-preloader-button="true"]', 'swPreloaderButton');
    });
});

if (window.EnderecoIntegrator) {
    window.EnderecoIntegrator = merge(EnderecoIntegrator, window.EnderecoIntegrator);
} else {
    window.EnderecoIntegrator = EnderecoIntegrator;
}

window.EnderecoIntegrator.asyncCallbacks.forEach(function (cb) {
    cb();
});
window.EnderecoIntegrator.asyncCallbacks = [];

window.EnderecoIntegrator.waitUntilReady().then(function () {
    //
});
