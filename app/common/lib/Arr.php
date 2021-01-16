<?php


namespace app\common\lib;


class Arr
{
    public static function getPageinateDefaultData($num){
        $result =  [
            "total" => 0,
            "per_page" => $num,
            "current_page" => 1,
            "last_page" => 0,
            "data" => []
        ];
        return $result;
    }
}