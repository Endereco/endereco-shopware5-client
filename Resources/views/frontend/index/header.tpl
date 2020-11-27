{extends file="parent:frontend/index/header.tpl"}

{block name="frontend_index_header_css_screen"}
    {$smarty.block.parent}

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
{/block}
