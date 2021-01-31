<?php
const URL = 'http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2020/%s';

function getOrMakePath(string $path)
{
    clearstatcache();
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}

function getProvice($url)
{
    $pReg = "/<a href='((\d+)\.html)'>(\S+?)<br\/>/";
    $proviceUrl = sprintf(URL, 'index.html');
    $content = file_get_contents($proviceUrl);
    $content = iconv('gb2312', 'utf-8//IGNORE', $content);
    preg_match_all($pReg, $content, $match);
    $arr = [];
    foreach ($match[1] as $index => $path) {
        $city = getCity($path);
        $arr[] = [
            'code' => $match[2][$index],
            'name' => $match[3][$index],
            'path' => $path,
            'city' => $city
        ];
    }
    print_r($arr);
}

getProvice($url);

function getCity($path)
{
    $cReg = "/<td><a href='([^']+)'>(\d+)<\/a><\/td><td><a href='[^']+'>(.*?)<\/a><\/td>/";
    $cityUrl = sprintf(URL, $path);
    $content = file_get_contents($cityUrl);
    $content = iconv('gb2312', 'utf-8//IGNORE', $content);
    preg_match_all($cReg, $content, $match);
    $arr = [];
    foreach ($match[1] as $index => $path) {
        $city = getCounty($path);
        $arr[] = [
            'code' => $match[2][$index],
            'name' => $match[3][$index],
            'path' => $path,
            'city' => $city
        ];
    }
    return $arr;
}


function getCounty($path)
{
    $cReg = "/<td><a href='([^']+)'>(\d+)<\/a><\/td><td><a href='[^']+'>(.*?)<\/a><\/td>/";
    $url = sprintf(URL, $path);
    $content = file_get_contents($url);
    $content = iconv('gb2312', 'utf-8//IGNORE', $content);
    preg_match_all($cReg, $content, $match);
    $arr = [];
    foreach ($match[1] as $index => $path) {
        $city = getTowntr($path);
        $arr[] = [
            'code' => $match[2][$index],
            'name' => $match[3][$index],
            'path' => $path,
            'city' => $city
        ];
    }
    return $arr;
}

function getTowntr($path)
{
    $cReg = "/<td><a href='([^']+)'>(\d+)<\/a><\/td><td><a href='[^']+'>(.*?)<\/a><\/td>/";
    $url = sprintf(URL, $path);
    $content = file_get_contents($url);
    $content = iconv('gb2312', 'utf-8//IGNORE', $content);
    preg_match_all($cReg, $content, $match);
    $arr = [];
    foreach ($match[1] as $index => $path) {
        $city = getVillagetr($path);
        $arr[] = [
            'code' => $match[2][$index],
            'name' => $match[3][$index],
            'path' => $path,
            'city' => $city
        ];
    }
    print_r($arr);exit;
    return $arr;
}

function getVillagetr($path)
{
    $vReg = "/<tr[^>]+><td>(\d+)<\/td><td>[\d]+<\/td><td>(.*?)<\/td><\/tr>/";
    $url = sprintf(URL, $path);
    $content = file_get_contents($url);
    $content = iconv('gb2312', 'utf-8//IGNORE', $content);
    preg_match_all($vReg, $content, $match);
    $arr = [];
    foreach ($match[1] as $index => $path) {
        $arr[] = [
            'code' => $match[2][$index],
            'name' => $match[3][$index],
            'path' => $path,
        ];
    }
    return $arr;
}
