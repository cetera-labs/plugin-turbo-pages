<?php

namespace TurboPages;

class Export {

    use \Cetera\DbConnection;

    private static function protocol() {
        
        return !empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']) ? 'https://' : 'http://';

    }

    private static function relink(String $link): String {

        $result = str_replace('http://', self::protocol(), $link);
        
        //delete filename from path
        $lastslash = strrpos($result, DIRECTORY_SEPARATOR);
        $result = substr($result, 0, $lastslash + 1);

        return $result;
    }

    private static function addChannel(\TurboPages\TurboPager $tp, \Cetera\Catalog $catalog) {
        
        $link = self::protocol() . $catalog->fields['alias'];
        $title = $catalog->fields['meta_title'];

        $tp->addChannel($link, $title);

    }

    private static function addItem(\TurboPages\TurboPager $tp, \Cetera\Material $material) {
        
        $link = self::relink($material->getFullUrl());
        $title = htmlspecialchars($material->fields['name']);
        $content = htmlspecialchars($material->fields['text']);

        $tp->addItem($link, $title, $content);

    }

    private static function isValidCatalog(\Cetera\Catalog $catalog, Array $excludedCatalogIDs): bool {
        
        $path = $catalog->getPath();

        foreach ($path as $node) {
            if (in_array($node->id, $excludedCatalogIDs)) {
                return false;
            }
        }

        return true;
    }

    private static function isValidMaterial(\Cetera\Material $material, Array $excludedMaterialIDs): bool {
        
        return !in_array($material->id, $excludedMaterialIDs) && $material->getType() == MATH_PUBLISHED;

    }

    private static function getAllCatalogs(): \Cetera\Iterator\Catalog\Catalog {
        
        $allCatalogs = new \Cetera\Iterator\Catalog\Catalog();
        $allCatalogs->orderBy('b.lft', 'ASC');
        
        return $allCatalogs;

    }

    public static function run() {
        
        $excludedCatalogIDs = \TurboPages\Options::getDirIDs();
        $excludedMaterialIDs = \TurboPages\Options::getMaterialIDs();

        $allCatalogs = self::getAllCatalogs();

        $tp = new \TurboPages\TurboPager();

        foreach ($allCatalogs as $catalog) {

            if ($catalog->isServer()) {        
                self::addChannel($tp, $catalog);
                continue;
            }    
                
            if (self::isValidCatalog($catalog, $excludedCatalogIDs)) {
                
                $materials = $catalog->getMaterials();

                foreach ($materials as $material) {
                    
                    if (self::isValidMaterial($material, $excludedMaterialIDs)) {
                        self::addItem($tp, $material);
                    }

                }

            }

        }

        try {
            $filename = \TurboPages\Options::getFilename();
            file_put_contents(DOCROOT . $filename, $tp);
            $error = false;
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        } finally {
            return $error;
        }

    }
}