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

$export = new \TurboPages\Export();
$error = $export->run();

echo json_encode($error);

?>