<?php

namespace TurboPages;

class Catalogs {

    public static function get(Array $excludedCatalogIDs): Array {

        $allCatalogs = new \Cetera\Iterator\Catalog\Catalog();
        $allCatalogs->orderBy('b.lft', 'ASC');

        $catalogs = [];
        
        $serverIndex = -1;
        foreach ($allCatalogs as $catalog) {
            if ($catalog->isServer()) {
                $catalogs[] = [$catalog];
                $serverIndex++;
            } else {
                if (self::isValid($catalog, $excludedCatalogIDs)) {
                    $catalogs[$serverIndex][] = $catalog;
                }
            }

        }
       
        return $catalogs;

    }

    private static function isValid(\Cetera\Catalog $catalog, Array $excludedCatalogIDs): bool {

        $path = $catalog->getPath();

        foreach ($path as $node) {
            if (in_array($node->id, $excludedCatalogIDs)) {
                return false;
            }
        }

        return true;
    }

}