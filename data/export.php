<?php
/************************************************************************************************
 *
 * Выгрузка
 *************************************************************************************************/

include_once('common_bo.php');

$filename = $_REQUEST['param'] ?? \TurboPages\Options::getFilename();
$protocol = (bool) ($_REQUEST['protocol'] ?? \TurboPages\Options::getProtocol());

switch (true) {
    case empty($filename):
        $error = 'Filename is empty';
        break;
    default:
        \TurboPages\Options::setFilename($filename);
        \TurboPages\Options::setProtocol($protocol);
        
        $export = new \TurboPages\Export();
        $error = $export->run();
}

echo json_encode($error);

?>