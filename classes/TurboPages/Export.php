<?php

namespace TurboPages;

class Export {

    use \Cetera\DbConnection;

    private TurboPager $tp;
    private Array $excludedCatalogIDs;
    private Array $excludedMaterialIDs;
    private \Cetera\Iterator\Catalog\Catalog $allCatalogs;
    private String $protocol;

    public function __construct() {
        
        $this->tp = new \TurboPages\TurboPager();
        $this->excludedCatalogIDs = \TurboPages\Options::getDirIDs();
        $this->excludedMaterialIDs = \TurboPages\Options::getMaterialIDs();
        $this->allCatalogs = \TurboPages\Options::getAllCatalogs();
        $this->protocol = \TurboPages\Options::getProtocol() ? 'https://' : 'http://';
        
    }

    public function run() {
        
        try {
            $this->prepareData();
            $filename = \TurboPages\Options::getFilename();
            file_put_contents(DOCROOT . $filename, $this->tp);
            $error = false;
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        } finally {
            return $error;
        }

    }

    private function prepareData() {

        while ($this->allCatalogs->valid()) {

            $catalog = $this->allCatalogs->current();

            if (in_array($catalog->id, $this->excludedCatalogIDs) || $catalog->isHidden()) {

                $level = $catalog->level;

                do {
                    $this->allCatalogs->next();
                    $catalog = $this->allCatalogs->current();
                } while ($level < $catalog->level);
                
                continue;

            }
            
            if ($catalog->isServer()) {        
                $this->addChannel($catalog);                
            }
                
            $materials = $catalog->getMaterials();

            foreach ($materials as $material) {
                
                if (!in_array($material->id, $this->excludedMaterialIDs) && $material->getType() == MATH_PUBLISHED) {
                    $this->addItem($material);
                }

            }

            $this->allCatalogs->next();
 
        }

    }

    private function relink(String $link): String {

        $link =str_replace('http://', $this->protocol, $link);
       
        //delete filename from path
        $lastslash = strrpos($link, DIRECTORY_SEPARATOR);
        $result = substr($link, 0, $lastslash + 1);

        return $result;
    }

    private function addChannel(\Cetera\Catalog $catalog) {
        
        $link = $this->protocol . $catalog->fields['alias'];
        $title = $catalog->fields['meta_title'];

        $this->tp->addChannel($link, $title);

    }

    private function addItem(\Cetera\Material $material) {
        
        $link = $this->relink($material->getFullUrl());
        $title = htmlspecialchars($material->fields['name']);
        $content = htmlspecialchars($material->fields['text']);

        $this->tp->addItem($link, $title, $content);

    }

}