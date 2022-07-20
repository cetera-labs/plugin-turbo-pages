<?php

namespace TurboPages;

class Catalogs {

    public static function get(): Array {
        
        $root = \Cetera\Catalog::getRoot();

        $servers = $root->getChildren();

        $excludedCatalogs = \TurboPages\Options::getDirIDs();

        $catalogs = [];

        foreach ($servers as $server) {
            $catalogs[] = self::getSubCatalogs($server, $excludedCatalogs);
        }

        return $catalogs;

    }

    private static function getSubCatalogs(\Cetera\Catalog $catalog, Array $excludedCatalogs): Array {

        switch (true) {
            case in_array($catalog->id, $excludedCatalogs):
                return [];
            case $catalog->isLink():
                return [];
            case $catalog->isHidden():
                return [];
        }

        $subCatalogsLocal = $catalog->getChildren()->orderBy('b.lft', 'ASC');

        $subCatalogs = [$catalog];

        foreach ($subCatalogsLocal as $subCatalogLocal) {
            $subCatalogs = [...$subCatalogs, ...self::getSubCatalogs($subCatalogLocal, $excludedCatalogs)];
        }

        return $subCatalogs;

    }
}