<?php
$pReg = "<a href='((\d+)\.html)'>(\S+?)<br\/>";
$url = 'http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2020';
$content = file_get_contents($url);