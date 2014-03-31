<?php

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$address = gethostbyname('localhost');
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

$s_id = pack("l", 5);
$u_id = pack("l", 1);

$filename = "example.py";
socket_write($socket, $s_id);
socket_write($socket, $u_id);
socket_write($socket, $filename, strlen($filename));
socket_close($socket);

?>