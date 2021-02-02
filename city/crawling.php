<?php

class City
{

    const URL = 'http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2020/%s';

    public $code = '';

    function getOrMakePath(string $path)
    {
        clearstatcache();
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    //http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2020/index.html
    function getProvice()
    {
        $pReg = "/<a href='((\d+)\.html)'>(\S+?)<br\/>/";
        $url = sprintf(self::URL, 'index.html', '');
        $content = $this->get($url);
        $content = iconv('gb2312', 'utf-8//IGNORE', $content);
        preg_match_all($pReg, $content, $match);
        $arr = [];
        foreach ($match[1] as $index => $path) {
            $this->code = $match[2][$index];
            $city = $this->getCity($path);
            $arr[] = [
                'code' => $match[2][$index],
                'name' => $match[3][$index],
                'path' => $path,
                'city' => $city
            ];
            print_r($arr);
            exit;
        }
    }

    //http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2020/11.html
    function getCity($path)
    {
        $cReg = "/<td><a href='([^']+)'>(\d+)<\/a><\/td><td><a href='[^']+'>(.*?)<\/a><\/td>/";
        $url = sprintf(self::URL, $path, '');
        $content = $this->get($url);
        $content = iconv('gb2312', 'utf-8//IGNORE', $content);
        preg_match_all($cReg, $content, $match);
        $arr = [];
        foreach ($match[1] as $index => $path) {
            $city = $this->getCounty($path);
            $arr[] = [
                'code' => $match[2][$index],
                'name' => $match[3][$index],
                'path' => $path,
                'city' => $city
            ];
        }
        return $arr;
    }


    //http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2020/11/1101.html
    function getCounty($path)
    {
        $cReg = "/<td><a href='([^']+)'>(\d+)<\/a><\/td><td><a href='[^']+'>(.*?)<\/a><\/td>/";
        $url = sprintf(self::URL, $path);
        $content = $this->get($url);
        $content = iconv('gb2312', 'utf-8//IGNORE', $content);
        preg_match_all($cReg, $content, $match);
        $arr = [];
        foreach ($match[1] as $index => $path) {
            $city = $this->getTowntr($path);
            $arr[] = [
                'code' => $match[2][$index],
                'name' => $match[3][$index],
                'path' => $path,
                'city' => $city
            ];
        }
        return $arr;
    }

    //http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2020/11/01/110101.html
    function getTowntr($path)
    {
        $cReg = "/<td><a href='([^']+)'>(\d+)<\/a><\/td><td><a href='[^']+'>(.*?)<\/a><\/td>/";
        $path = $this->code . DIRECTORY_SEPARATOR . $path;
        $url = sprintf(self::URL, $path);
        $content = $this->get($url);
        $content = iconv('gb2312', 'utf-8//IGNORE', $content);
        preg_match_all($cReg, $content, $match);
        $arr = [];
        foreach ($match[1] as $index => $path) {
            $city = $this->getVillagetr($path);
            $arr[] = [
                'code' => $match[2][$index],
                'name' => $match[3][$index],
                'path' => $path,
                'city' => $city
            ];
        }
        return $arr;
    }

    //http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2020/11/01/01/110101001.html
    function getVillagetr($path)
    {
        $vReg = "/<tr[^>]+><td>(\d+)<\/td><td>[\d]+<\/td><td>(.*?)<\/td><\/tr>/";
        $path = $this->code . '/01' . DIRECTORY_SEPARATOR . $path;
        $url = sprintf(self::URL, $path);
        $content = $this->get($url);
        $content = iconv('gb2312', 'utf-8//IGNORE', $content);
        preg_match_all($vReg, $content, $match);
        $arr = [];
        foreach ($match[1] as $index => $path) {
            $arr[] = [
                'code' => $match[1][$index],
                'name' => $match[2][$index],
                'path' => $path,
            ];
        }
        return $arr;
    }

    public function get(string $url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        if (curl_error($ch)) {
            echo "Error:" . curl_error($ch);
            die();
        }
        curl_close($ch);
        return $data;
    }
}

(new City)->getProvice();
