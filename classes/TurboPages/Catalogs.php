<?php

namespace TurboPages;

class Catalogs {

    public static function get(): Array {
        
        $root = \Cetera\Catalog::getRoot();

        $servers = $root->getChildren();

        $excludedCatalogs = \TurboPages\Options::getDirIDs();

        $catalogs = [];

        foreach ($servers as $server) {
            $catalogs[] = self::getSubCatalogsIDs($server, $excludedCatalogs);
        }

        return $catalogs;

    }

    private static function getSubCatalogsIDs(\Cetera\Catalog $catalog, Array $excludedCatalogs): Array {

        switch (true) {
            case in_array($catalog->id, $excludedCatalogs):
                return [];
            case $catalog->isLink():
                return [];
            case $catalog->isHidden():
                return [];
        }

        $subCatalogs = $catalog->getChildren();

        $ids = [$catalog->id];

        foreach ($subCatalogs as $subCatalog) {
            $ids = [...$ids, ...self::getSubCatalogsIDs($subCatalog, $excludedCatalogs)];
        }

        return $ids;

    }
}