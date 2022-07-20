<?php

namespace TurboPages;

class Export {

    use \Cetera\DbConnection;

    private static function relink(String $link): String {
       
        //delete filename from path
        $lastslash = strrpos($link, DIRECTORY_SEPARATOR);
        $result = substr($link, 0, $lastslash + 1);

        return $result;
    }

    private static function addChannel(\TurboPages\TurboPager $tp, \Cetera\Catalog $catalog) {
        
        $link = 'hhtp://' . $catalog->fields['alias'];
        $title = $catalog->fields['meta_title'];

        $tp->addChannel($link, $title);

    }

    private static function addItem(\TurboPages\TurboPager $tp, \Cetera\Material $material) {
        
        $link = self::relink($material->getFullUrl());
        $title = htmlspecialchars($material->fields['name']);
        $content = htmlspecialchars($material->fields['text']);

        $tp->addItem($link, $title, $content);

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

        while ($allCatalogs->valid()) {

            $catalog = $allCatalogs->current();

            if (in_array($catalog->id, $excludedCatalogIDs) || $catalog->isHidden()) {

                $level = count($catalog->getPath());

                //skip self
                $allCatalogs->next();
                $catalog = $allCatalogs->current();

                //skip children
                while ($level < count($catalog->getPath())) {
                    $allCatalogs->next();
                    $catalog = $allCatalogs->current();
                }
                
                continue;

            }
            
            if ($catalog->isServer()) {        
                self::addChannel($tp, $catalog);
                $allCatalogs->next();
                continue;
            }
                
            $materials = $catalog->getMaterials();

            foreach ($materials as $material) {
                
                if (self::isValidMaterial($material, $excludedMaterialIDs)) {
                    self::addItem($tp, $material);
                }

            }

            $allCatalogs->next();

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