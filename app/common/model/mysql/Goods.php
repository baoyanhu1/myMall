<?php


namespace app\common\model\mysql;


use think\facade\Request;

class Goods extends BaseModel
{
    /**
     * 使用withsearch模糊查询商品名称
     * @param $query
     * @param $value
     */
    public function searchTitleAttr($query,$value){
        $query->where("title","like","%".$value."%");
    }

    /**
     * 使用withsearch按创建商品时间查询
     * @param $query
     * @param $value
     */
    public function searchCreateTimeAttr($query,$value){
        $query->whereBetweenTime("create_time",$value[0],$value[1]);
    }
    /**
     * 分页查询商品
     * @param $num
     * @return \think\Paginator
     * @throws \think\db\exception\DbException
     */
    public function getLists($likeKeys,$data,$num,$where){
        if (!empty($likeKeys)){
            $res = $this->withSearch($likeKeys,$data);
        }else{
            $res = $this;
        }
        $order = [
            'listorder' => 'asc',
            'id' => 'asc'
        ];

        $result = $res->where($where)
            ->whereIn('status',[0,1])
            ->order($order)
            ->paginate($num);
        return $result;
    }

    /**
     * 更新分类排序数值
     * @param $data
     */
    public function changeStatus($data){
        $save_data = [
            'status' => $data['status'],
            'update_time' => time()
        ];
        $result = $this->where('id',$data['id'])->save($save_data);
        return $result;
    }

    /**
     * 获取是否有已开启秒杀商品
     * @param $data
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function isSpikeGoods($data){
        $where = [
            "is_spike" => config("status.mysql.is_spike"),
            "status" => config("status.mysql.table_normal"),
        ];
        $result = $this->where('id',"<>",$data['id'])
        ->where($where)->select();
        return $result;
    }

    /**
     * 获取首页轮播图
     * @param $field
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getRotationChart($field){
        $where = [
            "is_index_recommend" => config("status.mysql.table_normal"),
            "status" => config("status.mysql.table_normal"),
        ];
        $order = [
            "listorder" => "asc",
            "id" => "asc"
        ];
        $result = $this->where($where)
            ->field($field)
            ->order($order)
            ->limit(5)
            ->select();
        return $result;
    }

    /**
     * 将查询出的图片地址带域名返回(现图片已存入oss)
     * @param $value
     * @return string
     */
//    public function getImageAttr($value){
////        return request()->domain().$value;
//        return "http://localhost".$value;
//    }

    /**
     * 将查询出的轮播图循环带域名返回(现图片已存入oss)
     * @param $value
     * @return false|string[]
     */
//    public function getCarouselImageAttr($value){
//        if (!empty($value)){
//            $value = explode(",",$value);
//            $value = array_map(function ($v){
//                return "http://localhost".$v;
//            },$value);
//        }
//        return $value;
//    }

    /**
     * 按分类ID查询商品（当前用户首页栏目推荐商品）
     * @param $categoryId
     * @param $field
     * @param int $limit
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGoodsByCategoryId($categoryId,$field,$limit = 10){
        $order = [
            "listorder" => "asc",
            "id" => "desc"
        ];
        $where = [
            "is_spike" => config("status.mysql.is_no_spike"),
            "status" => config("status.mysql.table_normal"),
        ];
        $result = $this->whereFindInSet("category_path_id",$categoryId)
            ->where($where)
            ->field($field)
            ->order($order)
            ->limit($limit)
            ->select();
        return $result;
    }

    /**
     * 按分类查询商品数据/按关键字搜索商品
     * @param $categoryId
     * @param $field
     * @param $pageSize
     * @param $order
     * @return \think\Paginator
     * @throws \think\db\exception\DbException
     */
    public function getGoodsLists($categoryId,$field,$pageSize,$order,$keywords){
        $where = [
            "is_spike" => config("status.mysql.is_no_spike"),
            "status" => config("status.mysql.table_normal"),
        ];
        $res = $this;
        if ($categoryId){
            $res = $this->whereFindInSet("category_path_id",$categoryId);
        }
        if (!empty($keywords)){
            $data = [
                "title" => $keywords
            ];
            $res = $this->withSearch("title",$data);
        }
        $result = $res->where($where)
            ->field($field)
            ->order($order)
            ->paginate($pageSize);
        return $result;
    }
}