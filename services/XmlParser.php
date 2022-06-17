<?php

namespace app\services;

use yii\base\BaseObject;
use yii\httpclient\ParserInterface;

class XmlParser extends BaseObject implements ParserInterface
{
    public function parse($source)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($source);
        return $this->convertXmlToArray(simplexml_import_dom($dom->documentElement));
    }

    protected function convertXmlToArray($xml)
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        $result = (array) $xml;
        foreach ($result as $key => $value) {
            if (!is_scalar($value)) {
                $result[$key] = $this->convertXmlToArray($value);
            }
        }
        return $result;
    }
}