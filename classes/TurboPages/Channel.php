<?php

namespace TurboPages;

class Channel {

    public static function Add(\SimpleXMLElement $root, int $id, Array $excludedIDs) {
        $catalog = \Cetera\Catalog::getByID($id);
        $channel = $root->addChild('channel');
        $channel->addChild('link', 'https://' . $catalog->fields['alias']);
        $channel->addChild('title', $catalog->fields['meta_title']);

        $subMaterialIDs = \TurboPages\Catalog::getSubMaterialIDs($catalog);
        foreach ($subMaterialIDs as $id) {
            if (!in_array($id, $excludedIDs)) {
                \TurboPages\Item::Add($channel, $id);
            }
        }
    }

}