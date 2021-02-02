<?php


namespace app\common\business;

require_once "BusBase.php";
require_once __DIR__ . "/../model/mysql/Specs.php";
class GoodsSplikeOrder extends BusBase
{
    public function createGoodsSplikeOrder($orderData){
        $specs = new \app\common\model\mysql\Specs();
        $allSpecs = $specs->getAllSpecs();

        file_put_contents("ddddd.txt",json_encode($allSpecs->toArray()));
        file_put_contents("ffff.txt","fffff");
        file_put_contents("rraaaa.txt",$orderData);
    }
}