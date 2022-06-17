<?php

namespace app\services;

use yii\web\BadRequestHttpException;

class MainService
{
    private $xmlParserl;

    public function __construct(XmlParser $xmlParser)
    {
        $this->xmlParserl = $xmlParser;
    }

    public function getData($fileName, $separator = ' - > ', $breakingString = ' Breaking Point', $backString = ' rollback')
    {
        $data = $this->prepareArray($fileName);
        $countItems = count($data);

        $firstItem = $data[0];
        $lastItem = end($data);

        $firstBoard = explode('/', $firstItem['board']);
        $lastBoard = explode('/', $lastItem['off']);

        if ($firstBoard != $lastBoard) {
            return $this->getBrakedRoutes($data, $countItems, $separator, $backString);
        } else {
            return $this->getRoutes($data, $countItems, $separator);
        }
    }

    private function prepareArray($fileName)
    {
        $xmlData = $this->parseFile($fileName);

        if (!isset($xmlData['AirSegments']['AirSegment'])) {
            throw new BadRequestHttpException('Missing required data');
        }

        $data = [];

        foreach ($xmlData['AirSegments']['AirSegment'] as $key => $item) {
            $data[$key]['departure'] = $item['Departure']['@attributes']['Date'] . ' ' . $item['Departure']['@attributes']['Time'];
            $data[$key]['arrival'] = $item['Arrival']['@attributes']['Date'] . ' ' . $item['Arrival']['@attributes']['Time'];
            $data[$key]['board'] = $item['Board']['@attributes']['City'];
            $data[$key]['off'] = $item['Off']['@attributes']['City'];
        }

        // sorting items
        usort($data, function($a, $b) {
            return strtotime($a['departure']) - strtotime($b['departure']);
        });

        return $data;
    }

    private function parseFile($fileName)
    {
        return $this->xmlParserl->parse(file_get_contents(\Yii::getAlias('@webroot') . $fileName));
    }

    private function getBrakedRoutes($data,$countItems,$separator,$backString)
    {
        $string = '';
        $tempData = [];
        foreach ($data as $key => $item) {
            $citesList = $this->getCitesList($item);

            if (in_array($citesList['city'], $tempData)) {
                $string .= $citesList['city'] . $backString . ' ';
            } else {
                if ($countItems == $key + 1) {
                    $string .= $citesList['city'] . $separator . $citesList['off'];
                } else {
                    $string .= $citesList['city'] . $separator;
                }
            }
            array_push($tempData, $citesList['city']);
        }
        return $string;
    }

    private function getRoutes($data, $countItems, $separator)
    {
        $string = '';
        foreach ($data as $key => $item) {
            $citesList = $this->getCitesList($item);

            if ($countItems == $key + 1) {
                $string .=  $citesList['city'] . $separator . $citesList['off'];
            } else {
                $string .=  $citesList['city'] . $separator;
            }
        }
        return $string;
    }

    private function getCitesList(array $item) : array
    {
        $city = explode('/', $item['board']);
        $off = explode('/', $item['off']);

        $city = array_shift($city);
        $off = array_shift($off);

        return ['city' => $city, 'off' => $off];
    }
}