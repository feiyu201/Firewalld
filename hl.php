<?php
error_reporting(0);
header("Content-type:application/json");
require_once 'gn.class.php';
$rmb=$_GET['rmb'];
if(empty($rmb)) $rmb=1000;
$gn = new gn();
$url="https://www.pexpay.co/bapi/c2c/v1/friendly/c2c/ad/search";
$data='{"page":1,"rows":10,"payTypes":[],"classifies":[],"asset":"USDT","tradeType":"SELL","fiat":"CNY","publisherType":null,"filter":{"payTypes":[]},"transAmount":"'.$rmb.'"}';
$xforip = rand(1, 254) . "." . rand(1, 254) . "." . rand(1, 254) . "." . rand(1, 254);
    $aHeader = array("content-type: application/json", "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36", 'CLIENT-IP:' . $xforip, 'X-FORWARDED-FOR:' . $xforip); // 请求头信息
$data= $gn->http_post($url, $aHeader,$data);
$data = json_decode($data, true);
$xhsz=$data["data"];
$gs=count($xhsz);
foreach ($xhsz as $customer) {
$jsjg= $customer['adDetailResp']["price"]+$jsjg;

}
$wzxs="pexpay.co上USDT到人民币".$rmb."元的交易前10的平均汇率";
$sc='{"success":"1","result":{"status":"ALREADY","scur":"USDT","tcur":"CNY","ratenm":"'.$wzxs.'","rate":"'.round($jsjg/$gs, 2).'","update":"'.date('Y-m-d H:i:s', time()).'"}}';
echo $sc;