<?php

namespace TurboPages;

class TurboPage implements \Stringable {

    private \SimpleXMLElement $content;
    private \SimpleXMLElement $channel;

    public function __construct() {

        $this->content = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss />');
        $this->content->addAttribute('xmlns:xmlns:yandex', 'http://news.yandex.ru');
        $this->content->addAttribute('xmlns:xmlns:media', 'http://search.yahoo.com/mrss/');
        $this->content->addAttribute('xmlns:xmlns:turbo', 'http://turbo.yandex.ru');
        $this->content->addAttribute('version', '2.0');

    }

    public function __toString(): string {
        
        $dom = dom_import_simplexml($this->content)->ownerDocument;
        $dom->formatOutput = true;

        return html_entity_decode($dom->saveXML());

    }

    public function addChannel(Array $data) {
        
        $this->channel = $this->content->addChild('channel');
        $this->channel->addChild('link', $data['link']);
        $this->channel->addChild('title', $data['title']);

    }

    public function addItem(Array $data) {

        $item = $this->channel->addChild('item');
        $item->addAttribute('turbo', 'true');

        $item->addChild('link', $data['link']);
        $item->addChild('title', $data['title']);

        //CDATA
        $turbocontent = $item->addChild('turbo:turbo:content');

        $cheader = new \SimpleXMLElement('<header/>');
        $cheader->addChild('h1', $data['title']);

        $dom_cheader = dom_import_simplexml($cheader);
        $docOwner = $dom_cheader->ownerDocument;
        $docOwner->formatOutput = true;

        $res = $docOwner->saveXML($dom_cheader);
        $res = PHP_EOL . $res . PHP_EOL . $data['content'];

        $dom_turbocontent = dom_import_simplexml($turbocontent);
        $docOwner = $dom_turbocontent->ownerDocument;
        
        $cdata = $docOwner->createCDATASection($res);
        $dom_turbocontent->appendChild($cdata);

    }


}