<?php

namespace TurboPages;

class Material {
    
    use \Cetera\DbConnection;

    public static function getByID(int $id) {

        $query = self::getDbConnection()->createQueryBuilder();
        $result = $query
            ->select('idCat')
            ->from('materials')
            ->where('id="' . $id . '"' . ' and type&'.MATH_PUBLISHED.'=1')
            ->execute();

        $row = $result->fetch();
        $idCat = $row['idCat'];

        $oCatalog = \Cetera\Catalog::getByID($idCat);
        $oMaterial = $oCatalog->getMaterialById($id);

        $material = $oMaterial->fields;
        $material['path'] = preg_replace("#([^:])//#is", "$1/", $oCatalog->getUrl());
        $material['link'] = preg_replace("#([^:])//#is", "$1/", $oMaterial->getFullUrl());

        return $material;    
    }        
}

?>