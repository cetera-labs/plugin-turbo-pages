<?php

namespace TurboPages;

class TurboPager {

    use \Cetera\DbConnection;

    private static function relink(String $link): String {

        $result = str_replace('http://', 'https://', $link);
        
        //delete filename from path
        $lastslash = strrpos($result, DIRECTORY_SEPARATOR);
        $result = substr($result, 0, $lastslash + 1);

        return $result;
    }

    private static function addChannel(\TurboPages\TurboPage $tp, \Cetera\Catalog $catalog) {
        
        $data = [
            "link" => 'https://' . $catalog->fields['alias'],
            "title" => $catalog->fields['meta_title']
            ];
        $tp->addChannel($data);

    }

    private static function addItem(\TurboPages\TurboPage $tp, \Cetera\Material $material) {
        
        $data = [
            'link' => self::relink($material->getFullUrl()),
            'title' => htmlspecialchars($material->fields['name']),
            'content' => htmlspecialchars($material->fields['text']),
        ];
        $tp->addItem($data);

    }

    public static function export() {

        $excludedmaterialIDs = \TurboPages\Options::getMaterialIDs();
        
        $catalogsCollection = \TurboPages\Catalogs::get();
        $tp = new \TurboPages\TurboPage();

        foreach ($catalogsCollection as $catalogIDs) {

            for ($i = 0; $i < count($catalogIDs); $i++) { 
                
                $catalog = \Cetera\Catalog::getByID($catalogIDs[$i]);

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