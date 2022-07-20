<?php
/************************************************************************************************
 *
 * Выгрузка
 *************************************************************************************************/

include_once('common_bo.php');

$filename = $_REQUEST['param'] ?? false;

if ($filename && $filename !== '') {
    \TurboPages\Options::setFilename($filename);
} else {
    throw new \Exception('Filename is empty');
}

$error = \TurboPages\Export::run();

echo json_encode($error);

?>