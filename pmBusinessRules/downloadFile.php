<?php
    $type = '';
    switch ($_GET['type']) {
        case 'excel':
            $type = 'xls';
            break;
        case 'pmrl':
            $type = 'pmrl';
            break;
    }
    $filePath = PATH_FILES_BUSINESS_RULES . SYS_SYS . PATH_SEP . $type . PATH_SEP . $_GET['name'];
    G::streamFile($filePath, true);