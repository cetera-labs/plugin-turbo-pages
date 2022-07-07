<?php

namespace TurboPages;

class TurboPager {

    use \Cetera\DbConnection;

    private static function getExcludedIDs() {

        $dirIDs = \TurboPages\Options::getDirIDs();

        $subMaterialIDs = [];

        foreach ($dirIDs as $dirID) {
            $catalog = \Cetera\Catalog::getByID($dirID);
            $subMaterialIDs = [...$subMaterialIDs, ...\TurboPages\Catalog::getSubMaterialIDs($catalog)];
        }

        $materialIDs = \TurboPages\Options::getMaterialIDs();

        return array_unique([...$subMaterialIDs, ...$materialIDs]);

    }

    private static function toString() {
        
        $root = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss />');
        $root->addAttribute('xmlns:xmlns:yandex', 'http://news.yandex.ru');
        $root->addAttribute('xmlns:xmlns:media', 'http://search.yahoo.com/mrss/');
        $root->addAttribute('xmlns:xmlns:turbo', 'http://turbo.yandex.ru');
        $root->addAttribute('version', '2.0');

        $rootCatalog = \Cetera\Catalog::getRoot();

        $servers = $rootCatalog->getChildren();

        $excludedIDs = self::getExcludedIDs();

        foreach ($servers as $server) {
            \TurboPages\Channel::Add($root, $server->id, $excludedIDs);
        }

        $dom = dom_import_simplexml($root)->ownerDocument;
        $dom->formatOutput = true;

        return html_entity_decode($dom->saveXML());

    }

    public static function export() {

        try {
            $filename = \TurboPages\Options::getFilename();
            $content = self::toString();
            file_put_contents(DOCROOT . $filename, $content);
            $error = false;
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        } finally {
            return $error;
        }

    }
}

?>