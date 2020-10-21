<?php

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    $xml = simplexml_load_file(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/plugin.xml');
    $agent_info  = "Endereco Shopware5 Client (Download) v" . $xml->version;
    $post_data   = json_decode(file_get_contents('php://input'), true);
    $api_key     = trim($_SERVER['HTTP_X_AUTH_KEY']);
    $data_string = json_encode($post_data);
    $ch          = curl_init(trim($_SERVER['HTTP_X_REMOTE_API_URL']));

    if ($_SERVER['HTTP_X_TRANSACTION_ID']) {
        $tid = $_SERVER['HTTP_X_TRANSACTION_ID'];
    } else {
        $tid = 'not_set';
    }

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'X-Auth-Key: ' . $api_key,
            'X-Transaction-Id: ' . $tid,
            'X-Agent: ' . $agent_info,
            'X-Transaction-Referer: ' . $_SERVER['HTTP_X_TRANSACTION_REFERER'],
            'Content-Length: ' . strlen($data_string))
    );

    $result = curl_exec($ch);

    header('Content-Type: application/json');
    echo $result;
} else {
    echo 'We expect a POST request here.';
}

