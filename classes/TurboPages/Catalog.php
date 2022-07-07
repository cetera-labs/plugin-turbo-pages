<?php

namespace TurboPages;

class Catalog {

    private static function getLocalMaterials(\Cetera\Catalog $catalog) {
        
        $materials = $catalog->getMaterials();

        $materialIDs = [];

        foreach ($materials as $material) {
            if ((int)$material->getType() === 1) {
                $materialIDs[] = $material->id;
            }
        }

        return $materialIDs;
    }

    public static function getSubMaterialIDs(\Cetera\Catalog $catalog) {

        if ($catalog->isLink() || $catalog->isHidden()) {
            return [];
        }

        $oSubCatalogs = $catalog->getChildren();

        if (count($oSubCatalogs) === 0 ) {

            return self::getLocalMaterials($catalog);
        }

        $ids = [];

        foreach ($oSubCatalogs as $subCatalog) {
            $ids = [...$ids, ...self::getSubMaterialIDs($subCatalog)];
        }

        $ids = [...$ids, ...self::getLocalMaterials($catalog)];

        return $ids;
 
    }
}