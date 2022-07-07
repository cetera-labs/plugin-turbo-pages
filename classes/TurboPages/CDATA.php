<?php

namespace TurboPages;

class CDATA {

    public static function Add(\SimpleXMLElement $item, String $title, String $content) {
        $turbocontent = $item->addChild('turbo:turbo:content');

        $cheader = new \SimpleXMLElement('<header/>');
        $cheader->addChild('h1', $title);

        $dom_cheader = dom_import_simplexml($cheader);
        $docOwner = $dom_cheader->ownerDocument;
        $docOwner->formatOutput = true;

        $res = $docOwner->saveXML($dom_cheader);
        $res = PHP_EOL . $res . PHP_EOL . $content;

        $dom_turbocontent = dom_import_simplexml($turbocontent);
        $docOwner = $dom_turbocontent->ownerDocument;
        
        $cdata = $docOwner->createCDATASection($res);
        $dom_turbocontent->appendChild($cdata);
    }

}