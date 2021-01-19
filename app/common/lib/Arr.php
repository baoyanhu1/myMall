<?php


namespace app\common\lib;


class Arr
{
    /**
     * 返回指定格式分页
     * @param $num
     * @return array
     */
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

    /**
     * 数组排序
     * @param $result
     * @param $key
     * @param int $sort
     * @return array|bool
     */
    public static function sortArr($result,$key,$sort = SORT_DESC){
        if (!$result || !$key){
            return [];
        }
        $column = array_column($result,$key);
        array_multisort($column,$sort,$result);
        return $result;
    }
}