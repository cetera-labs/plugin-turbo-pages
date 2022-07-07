<?php

namespace TurboPages;

class Item {
    
    use \Cetera\DbConnection;

    public static function Add(\SimpleXMLElement $channel, int $id) {
        $material = \TurboPages\Material::getByID($id);
        $link = self::relink($material['link']);
        $title = htmlspecialchars($material['name']);
        $content = htmlspecialchars($material['text']);

        $item = $channel->addChild('item');
        $item->addAttribute('turbo', 'true');

        $item->addChild('link', $link);
        $item->addChild('title', $title);

        \TurboPages\CDATA::Add($item, $title, $content);

    }

    private static function relink(String $link) {
        $re = '/^^(http|https):\/\/(.+?)(\/index|)$/';
        preg_match($re, $link, $matches);
        return 'https://' . $matches[2] . '/';
    }

}

?>