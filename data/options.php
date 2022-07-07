<?php
/************************************************************************************************
 *
 * Настройки
 *************************************************************************************************/

include_once('common_bo.php');

$action = $_REQUEST['action'];
$from = $_REQUEST['from'];
$param = $_REQUEST['param'] ?? null;

switch ($action) {
    case 'getFilename':
        getFilename();
        break;
    case 'setFilename':
        setFileName($_REQUEST['param']);
        break;
    case 'getList':
        getList($from);
        break;
    case 'setList':
        setList($from, $param);
        break;
    case 'deleteID':
        deleteID($from, $param);
        break;        
    default:
        echo json_encode(array(
            'success' => false,
            'result' => 'default: ' . $action
        ));
}
die();

function getFilename() {
    $filename = \TurboPages\Options::getFilename();

    echo json_encode(array(
        'success' => true,
        'result' => $filename
    ));
}

function setFilename($filename) {
    $res = \TurboPages\Options::setFilename($filename);
    echo json_encode(array(
        'success' => true,
        'result' => $res
    ));
}

function getList($from) {

    switch ($from) {
        case 'dir_data':
            $ids = \TurboPages\Options::getDirIDs();
            break;
        case 'materials':
            $ids = \TurboPages\Options::getMaterialIDs();
            break;
        default:
    }

    $list = \TurboPages\View::get($ids, $from);

    echo json_encode($list);

}

function setList($from, $newIDs) {
    $newIDs = json_decode($newIDs);
    switch ($from) {
        case 'dir_data':
            $oldIDs = \TurboPages\Options::getDirIDs();
            $ids = [...$oldIDs, ...$newIDs];
            $res = \TurboPages\Options::setDirIDs($ids);
            break;
        case 'materials':
            $oldIDs = \TurboPages\Options::getMaterialIDs();
            $ids = [...$oldIDs, ...$newIDs];
            $res = \TurboPages\Options::setMaterialIDs($ids);
            break;
        default:
    }
}

function deleteID($from, $id) {
    switch ($from) {
        case 'dir_data':
            $ids = \TurboPages\Options::getDirIDs();
            unset($ids[array_search($id, $ids)]);
            $res = \TurboPages\Options::setDirIDs($ids);
            break;
        case 'materials':
            $ids = \TurboPages\Options::getMaterialIDs();
            unset($ids[array_search($id, $ids)]);
            $res = \TurboPages\Options::setMaterialIDs($ids);
            break;
        default:
    }

}

?>