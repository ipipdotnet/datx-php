<?php

require_once __DIR__ . '/autoload.php';

$bs = new ipip\datx\City("c:/work/tiantexin/17mon/mydata4vipday4.datx");
var_export($bs->find("223.220.233.0"));

$bs = new ipip\datx\District("c:/work/tiantexin/framework/library/ip/quxian.datx");
var_export($bs->find("1.12.29.0"));

$bs = new ipip\datx\BaseStation("c:/work/tiantexin/17mon/station_ip.datx");
var_export($bs->find("224.220.233.220"));