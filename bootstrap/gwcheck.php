<?php

# -- GATEWAY CHECK MODE --
# This mode merely responds with information about the request. This can be
# used by an operator or automated test suite to ensure that the channel
# through which requests reach the application code (web server, gateway
# interface) is properly set up

# * Mode enabling flag
# Check for flags that enable the operation of this mode
#  file: gwcheck.enabled , in the same folder along this file
#  environment: USH_PLATFORM_INSTALL_DEBUG_MODE_ENABLED variable
#    (NOTE that the .env file in the base folder is NOT parsed for this script!)
$enabled =
    file_exists(__DIR__ . '/install_debug_mode.enabled') ||
    ($_ENV['USH_PLATFORM_INSTALL_DEBUG_MODE_ENABLED'] ?? null);
if (!$enabled) {
    # While disabled, we indicate that in a special header
    header("X-Ushahidi-Platform-Install-Debug-Mode: off");
    http_response_code(204);
    exit();   # -- END request processing
}

# make the origin header handy
$origin = $_SERVER['HTTP_ORIGIN'] ?? null;

# * CORS pre-flight request mode check
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    # Check required headers
    $acr_method = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ?? null;
    $acr_headers = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ?? null;
    if ($origin && $acr_method && $acr_headers) {
        header("Access-Control-Allow-Origin: " . $origin);
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Authorization, Content-Type, Accept");
    }
    http_response_code(204);
    exit();   # -- END request processing
}

# * Normal mode
$response = [
    "api" => [
        "name" => "ushahidi:platform:gwcheck",
        "version" => "0.1"
    ],
    "data" => [
        "_GET" => $_GET,
        "_POST" => $_POST,
        "_REQUEST" => $_REQUEST
    ]
];

# Only return some keys from the $_SERVER superglobal
$server_keys = [
    'REQUEST_METHOD',
    'REQUEST_TIME',
    'QUERY_STRING',
    'HTTP_ACCEPT',
    'HTTP_ACCEPT_CHARSET',
    'HTTP_ACCEPT_ENCODING',
    'HTTP_ACCEPT_LANGUAGE',
    'HTTP_AUTHORIZATION',
    'HTTP_CONNECTION',
    'HTTP_HOST',
    'HTTP_ORIGIN',
    'HTTP_REFERER',
    'HTTP_USER_AGENT',
    'REMOTE_ADDR',
    'REQUEST_URI',
    'DOCUMENT_ROOT',
    'SCRIPT_FILENAME',
    'SCRIPT_NAME',
    'PATH_INFO',
    'ORIG_PATH_INFO'
];
# Include expected $_SERVER keys (null if undefined)
$response['data']["_SERVER"] = [];
foreach ($server_keys as $k) {
    $response['data']["_SERVER"][$k] = $_SERVER[$k] ?? null;
}

# Generate response
http_response_code(200);
header('Content-type: application/json');
if ($origin) {
    header('Access-Control-Allow-origin: ' . $origin);
}
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

# END request processing
exit();
