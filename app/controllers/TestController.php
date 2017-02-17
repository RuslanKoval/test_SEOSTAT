<?php

class TestController extends ApplicationController
{

    const STATUS_OK = 200;

    private $_presidents = [];


    public function indexAction()
    {
        $curl       = new Curl();
        $response   = $curl->get('http://2eu.kiev.ua/get_ads.php');

        $curlInfo   = $response['curl_info'];
        $status     = $response['status'];
        $headers    = $response['headers'];

        if ($status == self::STATUS_OK) {
            $data       = json_decode($response['data'], true);

            usort($data, function($a, $b){
                $conditions = ($a['price'] < $b['price'] );

                if ($a['price'] == $b['price']) {
                    $conditions = ($a['weight'] < $b['weight']);
                }

                return $conditions;
            });

            $this->view->data = $data;
        }


    }


    public function test2Action()
    {
        $this->getCSVContent();
        $min = $this->getMinYear();
        $max = $this->getMaxYear();
        $yearCount = [];
        for ($i = $min; $i <= $max; $i++) {
            $yearCount[] = [
                'year' => $i,
                'count' => $this->GetCountPresidents($i)
            ];
        }

        usort($yearCount, function($a, $b){
            return $a['count'] < $b['count'];
        });

        $this->view->data = $yearCount[0]['year'];
    }


    private function getMinYear() {
        $presidents = $this->_presidents;

        usort($presidents, function($a, $b){
            return $a['start'] > $b['start'];
        });

        return $presidents[0]['start'];
    }

    private function getMaxYear() {
        $presidents = $this->_presidents;

        usort($presidents, function($a, $b){
            return $a['end'] < $b['end'];
        });

        return $presidents[0]['end'];
    }

    private function getCSVContent() {
        $presidents = array_map('str_getcsv', file('presidents.csv'));

        array_walk($presidents, function(&$a) use ($presidents) {
            $a = array_combine($presidents[0], $a);
        });

        array_shift($presidents);

        $presidentsDates = [];

        foreach ($presidents as $key => $value) {
            $presidentsDates[$key]['start'] = $value['dateofBirth'];
            $presidentsDates[$key]['end'] = $value['dateofDeath'];
            if (!$value['dateofDeath']) {
                $presidentsDates[$key]['end'] = date('Y');
            }
        }
        $this->_presidents = $presidentsDates;

    }

    private function GetCountPresidents($year)
    {
        $count = 0;
        foreach ($this->_presidents as $president) {
            if ($year >= $president['start'] && $year <= $president['end']) {
                $count ++;
            }
        }
        return $count;
    }
}