{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_javascript_async_ready"}
    {$smarty.block.parent}
    {if $endereco_is_active && ({controllerName|lower}|in_array:$endereco_controller_whitelist)}
        <script async defer src="{link file="frontend/_public/src/js/endereco.min.js"}?ver={$endereco_plugin_version}"></script>
    {/if}

{/block}
