{extends file="parent:frontend/checkout/confirm.tpl"}

{block name='frontend_checkout_confirm_form'}
    {$smarty.block.parent}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        {if $endereco_need_to_reload}
            <script>
                location.reload();
            </script>
        {/if}
        <script>
            ( function() {
                $readyInterval = setInterval( function() {
                    if (document.readyState === 'complete') {
                        clearInterval($readyInterval);
                        var freeSlot = true;
                        if (!!('{$endereco_need_to_check_billing}')) {
                            var $billingInterval = setInterval( function() {
                                if (freeSlot) {
                                    freeSlot = false;
                                    document.querySelector('[data-sessionkey*="checkoutBillingAddressId"].btn').click();
                                    clearInterval($billingInterval);
                                }
                            }, 100)

                        }
                        if (!!('{$endereco_need_to_check_shipping}')) {
                            var $shippingInterval = setInterval( function() {
                                if (freeSlot) {
                                    freeSlot = false;
                                    document.querySelector('[data-sessionkey*="checkoutShippingAddressId"].btn').click();
                                    clearInterval($shippingInterval);
                                }
                            }, 100)
                        }

                        setInterval(function() {
                            if (!freeSlot && !document.querySelector('.js--overlay')) {
                                freeSlot = true;
                            }
                        }, 500)
                    }
                }, 300)
            })()
        </script>
    {/if}
{/block}
