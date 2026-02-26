<?php
function isLocalhost() {
    return in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);
}