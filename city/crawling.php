<?php
$url = 'http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2020/%s';

function getProvice($url)
{
    $pReg = "/<a href='((\d+)\.html)'>(\S+?)<br\/>/";
    $url = sprintf($url, 'index.html');
    $content = file_get_contents($url);
    $content = iconv('gb2312', 'utf-8//IGNORE', $content);
    preg_match_all($pReg, $content, $match);
    foreach ($match[1] as $url) {
        echo $url;
    }
    foreach ($match[2] as $code) {
        echo $code;
    }
    foreach ($match[3] as $name) {
        echo $name;
    }
}

function getCity($url, $path)
{
    $cReg = "/<td><a href='([^']+)'>(\d+)<\/a><\/td><td><a href='[^']+'>(.*?)<\/a><\/td>/";
    $url = sprintf($url, $path);
    $content = file_get_contents($url);
    $content = iconv('gb2312', 'utf-8//IGNORE', $content);
    preg_match_all($cReg, $content, $match);
    foreach ($match[1] as $url) {
        echo $url;
    }
    foreach ($match[2] as $code) {
        echo $code;
    }
    foreach ($match[3] as $name) {
        echo $name;
    }
}


function getCounty($url, $path)
{
    $cReg = "/<td><a href='([^']+)'>(\d+)<\/a><\/td><td><a href='[^']+'>(.*?)<\/a><\/td>/";
    $url = sprintf($url, $path);
    $content = file_get_contents($url);
    $content = iconv('gb2312', 'utf-8//IGNORE', $content);
    preg_match_all($cReg, $content, $match);
    foreach ($match[1] as $url) {
        echo $url;
    }
    foreach ($match[2] as $code) {
        echo $code;
    }
    foreach ($match[3] as $name) {
        echo $name;
    }
}

function getTowntr($url, $path)
{
    $cReg = "/<td><a href='([^']+)'>(\d+)<\/a><\/td><td><a href='[^']+'>(.*?)<\/a><\/td>/";
    $url = sprintf($url, $path);
    $content = file_get_contents($url);
    $content = iconv('gb2312', 'utf-8//IGNORE', $content);
    preg_match_all($cReg, $content, $match);
    foreach ($match[1] as $url) {
        echo $url;
    }
    foreach ($match[2] as $code) {
        echo $code;
    }
    foreach ($match[3] as $name) {
        echo $name;
    }
}

function getVillagetr($url, $path)
{
    $vReg = "/<tr[^>]+><td>(\d+)<\/td><td>[\d]+<\/td><td>(.*?)<\/td><\/tr>/";
    $url = sprintf($url, $path);
    $content = file_get_contents($url);
    $content = iconv('gb2312', 'utf-8//IGNORE', $content);
    preg_match_all($vReg, $content, $match);
    foreach ($match[1] as $url) {
        echo $url;
    }
    foreach ($match[2] as $code) {
        echo $code;
    }
}
