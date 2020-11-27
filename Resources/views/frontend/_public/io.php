<?php

$http = array(
    100 => 'HTTP/1.1 100 Continue',
    101 => 'HTTP/1.1 101 Switching Protocols',
    200 => 'HTTP/1.1 200 OK',
    201 => 'HTTP/1.1 201 Created',
    202 => 'HTTP/1.1 202 Accepted',
    203 => 'HTTP/1.1 203 Non-Authoritative Information',
    204 => 'HTTP/1.1 204 No Content',
    205 => 'HTTP/1.1 205 Reset Content',
    206 => 'HTTP/1.1 206 Partial Content',
    300 => 'HTTP/1.1 300 Multiple Choices',
    301 => 'HTTP/1.1 301 Moved Permanently',
    302 => 'HTTP/1.1 302 Found',
    303 => 'HTTP/1.1 303 See Other',
    304 => 'HTTP/1.1 304 Not Modified',
    305 => 'HTTP/1.1 305 Use Proxy',
    307 => 'HTTP/1.1 307 Temporary Redirect',
    400 => 'HTTP/1.1 400 Bad Request',
    401 => 'HTTP/1.1 401 Unauthorized',
    402 => 'HTTP/1.1 402 Payment Required',
    403 => 'HTTP/1.1 403 Forbidden',
    404 => 'HTTP/1.1 404 Not Found',
    405 => 'HTTP/1.1 405 Method Not Allowed',
    406 => 'HTTP/1.1 406 Not Acceptable',
    407 => 'HTTP/1.1 407 Proxy Authentication Required',
    408 => 'HTTP/1.1 408 Request Time-out',
    409 => 'HTTP/1.1 409 Conflict',
    410 => 'HTTP/1.1 410 Gone',
    411 => 'HTTP/1.1 411 Length Required',
    412 => 'HTTP/1.1 412 Precondition Failed',
    413 => 'HTTP/1.1 413 Request Entity Too Large',
    414 => 'HTTP/1.1 414 Request-URI Too Large',
    415 => 'HTTP/1.1 415 Unsupported Media Type',
    416 => 'HTTP/1.1 416 Requested Range Not Satisfiable',
    417 => 'HTTP/1.1 417 Expectation Failed',
    500 => 'HTTP/1.1 500 Internal Server Error',
    501 => 'HTTP/1.1 501 Not Implemented',
    502 => 'HTTP/1.1 502 Bad Gateway',
    503 => 'HTTP/1.1 503 Service Unavailable',
    504 => 'HTTP/1.1 504 Gateway Time-out',
    505 => 'HTTP/1.1 505 HTTP Version Not Supported',
);

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
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errorCode = curl_errno($ch);
    header('Content-Type: application/json');

    if (0 === $errorCode) {
        if ($http[$httpCode]) {
            header($http[$httpCode]);
        }
    } else {
        header($http[500]);
    }

    echo $result;
} else {
    echo 'We expect a POST request here.';
}

