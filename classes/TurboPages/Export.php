<?php

namespace TurboPages;

class Export {

    use \Cetera\DbConnection;

    private static function relink(String $link): String {

        $result = str_replace('http://', 'https://', $link);
        
        //delete filename from path
        $lastslash = strrpos($result, DIRECTORY_SEPARATOR);
        $result = substr($result, 0, $lastslash + 1);

        return $result;
    }

    private static function addChannel(\TurboPages\TurboPager $tp, \Cetera\Catalog $catalog) {
        
        $link = 'https://' . $catalog->fields['alias'];
        $title = $catalog->fields['meta_title'];
        
        $tp->addChannel($link, $title);

    }

    private static function addItem(\TurboPages\TurboPager $tp, \Cetera\Material $material) {
        
        $link = self::relink($material->getFullUrl());
        $title = htmlspecialchars($material->fields['name']);
        $content = htmlspecialchars($material->fields['text']);

        $tp->addItem($link, $title, $content);

    }

    public static function export() {

        $excludedmaterialIDs = \TurboPages\Options::getMaterialIDs();
        
        $catalogsCollection = \TurboPages\Catalogs::get();
        $tp = new \TurboPages\TurboPager();

        foreach ($catalogsCollection as $catalogs) {

            for ($i = 0; $i < count($catalogs); $i++) { 

                $catalog = $catalogs[$i];
                
                if ($i > 0) {
                    
                    $materials = $catalog->getMaterials();

                    foreach ($materials as $material) {
                        switch (true) {
                            case in_array($material->id, $excludedmaterialIDs):
                                break;
                            case $material->getType() != MATH_PUBLISHED:
                                break;
                            default:
                                self::additem($tp, $material);
                        }
                    }

                } else {
                    self::addChannel($tp, $catalog);
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