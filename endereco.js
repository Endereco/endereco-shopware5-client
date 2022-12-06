import Promise from 'promise-polyfill';
import merge from 'lodash.merge';
import EnderecoIntegrator from './node_modules/@endereco/js-sdk/modules/integrator';
import css from './endereco.scss';
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
        subdivisionCode: '[country_state_2]',
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
        firstName: '[firstname]',
        lastName: '[lastname]',
        title: '[title]'
    },
    emailServices: {
        email: '[email]'
    },
    phs: {
        phone: '[phone]'
    }
};

EnderecoIntegrator.css = css[0][1];
EnderecoIntegrator.resolvers.countryCodeWrite = function (value) {
    return new Promise(function (resolve, reject) {
        var key = window.EnderecoIntegrator.countryMapping[value.toUpperCase()];
        if (key !== undefined) {
            resolve(window.EnderecoIntegrator.countryMapping[value.toUpperCase()]);
        } else {
            resolve('');
        }
    });
}
EnderecoIntegrator.resolvers.countryCodeRead = function (value) {
    return new Promise(function (resolve, reject) {
        var key = window.EnderecoIntegrator.countryMappingReverse[value.toUpperCase()];
        if (key !== undefined) {
            resolve(window.EnderecoIntegrator.countryMappingReverse[value.toUpperCase()]);
        } else {
            resolve('');
        }
    });
}
EnderecoIntegrator.resolvers.countryCodeSetValue = function (subscriber, value) {
    subscriber.object.value = value;

    if (!!$) {
        $(subscriber.object).trigger('change');
    }
}

EnderecoIntegrator.resolvers.subdivisionCodeWrite = function (value) {
    return new Promise(function (resolve, reject) {
        var key = window.EnderecoIntegrator.subdivisionMapping[value];
        if (key !== undefined) {
            resolve(window.EnderecoIntegrator.subdivisionMapping[value]);
        } else {
            resolve('');
        }
    });
}
EnderecoIntegrator.resolvers.subdivisionCodeRead = function (value) {
    return new Promise(function (resolve, reject) {
        var key = window.EnderecoIntegrator.subdivisionMappingReverse[value.toUpperCase()];
        if (key !== undefined) {
            resolve(window.EnderecoIntegrator.subdivisionMappingReverse[value.toUpperCase()]);
        } else {
            resolve('');
        }
    });
}

EnderecoIntegrator.resolvers.salutationWrite = function (value) {
    var mapping = {
        'f': 'ms',
        'm': 'mr'
    };
    return new Promise(function (resolve, reject) {
        resolve(mapping[value]);
    });
}
EnderecoIntegrator.resolvers.salutationRead = function (value) {
    var mapping = {
        'ms': 'f',
        'mr': 'm'
    };
    return new Promise(function (resolve, reject) {
        resolve(mapping[value]);
    });
}

EnderecoIntegrator.afterAMSActivation.push( function(EAO) {

    // Dirty hack for shopware register button.
    if (!!EAO.onCloseModal) {
        EAO.onCloseModal.push(function(AddressObject) {
            if ($('.register--submit')) {
                $('.register--submit').each(function(i,e) {
                    if ($(e).data('plugin_swPreloaderButton')) {
                        $(e).data('plugin_swPreloaderButton').reset();
                    }
                });
            }
            if ($('.address--form-submit')) {
                $('.address--form-submit').each(function(i,e) {
                    if ($(e).data('plugin_swPreloaderButton')) {
                        $(e).data('plugin_swPreloaderButton').reset();
                    }
                });
            }
        });
    }

    if (!!EAO.onAfterAddressCheckSelected) {
        EAO.onAfterAddressCheckSelected.push(function(AddressObject) {
            if ($('.register--submit')) {
                $('.register--submit').each(function(i,e) {
                    if ($(e).data('plugin_swPreloaderButton')) {
                        $(e).data('plugin_swPreloaderButton').reset();
                    }
                });
            }
            if ($('.address--form-submit')) {
                $('.address--form-submit').each(function(i,e) {
                    if ($(e).data('plugin_swPreloaderButton')) {
                        $(e).data('plugin_swPreloaderButton').reset();
                    }
                });
            }
        });
    }

    if (!!EAO.onAfterAddressCheckNoAction) {
        EAO.onAfterAddressCheckNoAction.push(function(AddressObject) {
            if ($('.register--submit')) {
                $('.register--submit').each(function(i,e) {
                    if ($(e).data('plugin_swPreloaderButton')) {
                        $(e).data('plugin_swPreloaderButton').reset();
                    }
                });
            }
            if ($('.address--form-submit')) {
                $('.address--form-submit').each(function(i,e) {
                    if ($(e).data('plugin_swPreloaderButton')) {
                        $(e).data('plugin_swPreloaderButton').reset();
                    }
                });
            }
        });
    }

    if (!!EAO.onConfirmAddress) {
        EAO.onConfirmAddress.push(function(AddressObject) {
            if ($('.register--submit')) {
                $('.register--submit').each(function(i,e) {
                    if ($(e).data('plugin_swPreloaderButton')) {
                        $(e).data('plugin_swPreloaderButton').reset();
                    }
                });
            }
            if ($('.address--form-submit')) {
                $('.address--form-submit').each(function(i,e) {
                    if ($(e).data('plugin_swPreloaderButton')) {
                        $(e).data('plugin_swPreloaderButton').reset();
                    }
                });
            }
        });
    }

    if (!!EAO.onSubmitUnblock) {
        EAO.onSubmitUnblock.push(function(AddressObject) {
            if ($('.register--submit')) {
                $('.register--submit').each(function(i,e) {
                    if ($(e).data('plugin_swPreloaderButton')) {
                        $(e).data('plugin_swPreloaderButton').reset();
                    }
                });
            }
            if ($('.address--form-submit')) {
                $('.address--form-submit').each(function(i,e) {
                    if ($(e).data('plugin_swPreloaderButton')) {
                        $(e).data('plugin_swPreloaderButton').reset();
                    }
                });
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
                    window.EnderecoIntegrator.hasSubmit = true
                    setTimeout(function() {
                        EAO.util.checkAddress()
                            .catch(function() {
                                EAO.waitForAllPopupsToClose().then(function() {
                                    if (window.EnderecoIntegrator && window.EnderecoIntegrator.submitResume) {
                                        window.EnderecoIntegrator.submitResume();
                                    }
                                }).catch()
                            }).finally( function() {
                                window.EnderecoIntegrator.hasSubmit = false
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

if (!window.EnderecoIntegrator.onLoad) {
    window.EnderecoIntegrator.onLoad = [];
}

(function() {
   setInterval( function() {
       if (
           !!window.EnderecoIntegrator &&
           !!window.EnderecoIntegrator.integratedObjects &&
           typeof window.EnderecoIntegrator.integratedObjects === 'object'
       ) {
           Object.keys(window.EnderecoIntegrator.integratedObjects).forEach(function(key,index) {
               if (window.EnderecoIntegrator.integratedObjects[key].name === 'ams' &&
                   !window.EnderecoIntegrator.integratedObjects[key].hasBeenExtended
               ) {
                   window.EnderecoIntegrator.integratedObjects[key].onSubmitUnblock.push(function(EAO) {
                       EAO.forms.forEach( function(form) {
                           var submitButton = form.querySelector("[type=\"submit\"]:disabled");
                           if (!!submitButton) {
                               submitButton.disabled = false;
                           }
                       });
                       window.EnderecoIntegrator.integratedObjects[key].hasBeenExtended = true;
                   });
               }
           });
       }
   }, 1);
})();
