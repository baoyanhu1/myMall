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
    public function getLists($likeKeys,$data,$num){
        if (!empty($likeKeys)){
            $res = $this->withSearch($likeKeys,$data);
        }else{
            $res = $this;
        }
        $order = [
            'listorder' => 'asc',
            'id' => 'asc'
        ];
        $result = $res->whereIn('status',[0,1])
            ->order($order)
            ->paginate($num);
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
     * 将查询出的图片地址带域名返回
     * @param $value
     * @return string
     */
    public function getImageAttr($value){
//        return request()->domain().$value;
        return "http://localhost".$value;
    }
}