<?php
    function errorLog($error)

    {
        error_log($error, 3, '/var/log/apache2/what2watch_error.log');
    }

    set_error_handler('errorLog');
?>