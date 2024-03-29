<?php

namespace TurboPages;

class Export {

    use \Cetera\DbConnection;

    // private TurboPager $tp;
    private Array $excludedCatalogIDs;
    private Array $excludedMaterialIDs;
    private \Cetera\Iterator\Catalog\Catalog $allCatalogs;
    private String $protocol;
    private Array $servers;
    private String $currentServer;

    public function __construct() {
        
        $this->excludedCatalogIDs = \TurboPages\Options::getDirIDs();
        $this->excludedMaterialIDs = \TurboPages\Options::getMaterialIDs();
        $this->allCatalogs = \TurboPages\Options::getAllCatalogs();
        $this->protocol = \TurboPages\Options::getProtocol() ? 'https://' : 'http://';
        
    }

    public function run() {
        
        try {
            $this->prepareData();

            foreach ($this->servers as $server => $tp) {
             
                $filename = \TurboPages\Options::getFilename() . $server . '.xml';;
                file_put_contents(DOCROOT . $filename, $tp);
            }

            $status = ['error' => false, 'message' => count($this->servers)];
        } catch (\Throwable $e) {
            $status = ['error' => true, 'message' => $e->getMessage()];
        } finally {
            return $status;
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
                
            if ($catalog->materialsObjectDefinition) {
                foreach ($catalog->getMaterials() as $material) {

                    if (!in_array($material->id, $this->excludedMaterialIDs) && $material->getType() == MATH_PUBLISHED) {

                        $this->addItem($material);
                    }

                }
            }

            $this->allCatalogs->next();
 
        }
        

    }

    private function relink(String $link): String {

        $link =str_replace('http://', $this->protocol, $link);

        $index = 'index';
        $len = strlen($index);
        if (str_ends_with($link, $index)) {
            $link = substr($link, 0, strlen($link) - $len);
        }

        return $link;
    }

    private function addChannel(\Cetera\Catalog $catalog) {

        $this->currentServer = $catalog->fields['alias'];

        $tp = new \TurboPages\TurboPager();

        $link = $this->protocol . $catalog->fields['alias'];
        $title = $catalog->fields['meta_title'];
        $tp->addChannel($link, $title);
        $this->servers[$this->currentServer] = $tp;

    }

    private function addItem(\Cetera\Material $material) {

        $link = $this->relink($material->getFullUrl());
        $title = trim(htmlspecialchars($material->fields['name']));
        if ($title === '') {
            $title = htmlspecialchars($material->alias);
        }
        
        $content = $material->fields['text'] ?? '';                  
        $this->servers[$this->currentServer]->additem($link, $title, $content);

    }

}
