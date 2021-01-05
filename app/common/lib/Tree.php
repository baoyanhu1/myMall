<?php


namespace app\common\lib;


class Tree
{
    /**
     * 树状无限分类
     * @param $data
     */
    public static function getTree($data){
        $items = [];
        foreach ($data as $v){
            $items[$v['category_id']] = $v;
        }
        $tree = [];
        foreach ($items as $k=>$item){
            if (isset($items[$item['pid']])){
                $items[$item['pid']]['list'][] = &$items[$k];
            }else{
                $tree[] = &$items[$k];
            }
        }
        return $tree;
    }

    /**
     * 返回指定条数据
     * @param $data
     * @param int $first
     * @param int $second
     * @param int $three
     * @return array
     */
    public static function sliceTree($data,$first = 5,$second = 3,$three = 5){
        $data = array_slice($data,0,$first);
        foreach ($data as $k => $v){
            if (!empty($v['list'])){
                $data[$k]['list'] = array_slice($v['list'],0,$second);
                foreach ($v['list'] as $kk => $vv){
                    if (!empty($vv['list'])){
                        $data[$k]['list'][$kk]['list'] = array_slice($vv['list'],0,$three);
                    }
                }
            }
        }
        return $data;
    }
}