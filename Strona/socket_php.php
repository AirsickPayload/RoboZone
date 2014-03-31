<?php

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$address = gethostbyname('robozone.no-ip.biz');
$service_port=20001;

if ($socket === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
} else {
    echo "OK.\n";
}

echo "Attempting to connect to '$address' on port '$service_port'...";
$result = socket_connect($socket, $address, $service_port);
if ($result === false) {
    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
} else {
    echo "OK.\n";
}

$in='string testowy';

socket_write($socket, $in, strlen($in));

socket_close($socket);

?>