<?php


namespace app\common\model\mysql;


use think\Model;

class Category extends BaseModel
{
    //    写入数据时自动写入创建时间和更新时间
    protected $autoWriteTimestamp = true;

    /**
     * 根据分类名称获取数据
     * @param $data
     * @return array|bool|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCategoryInfo($data){
        if (empty($data)){
            return false;
        }
        $where = [
            "name" => $data['name']
        ];
        $result = $this->where($where)->find();
        return $result;
    }

    /**
     * 获取所有分类数据
     */
    public function getCategorys($fileds = '*'){
        $where = [
            'status' => config("status.mysql.table_normal")
        ];
        $order = [
            'listorder' => 'desc',
            'id' => 'desc'
        ];
        $result = $this->where($where)
            ->field($fileds)
            ->order($order)
            ->select();
        if (!$result){
            return [];
        }
        return $result->toArray();
    }

    /**
     * 获取分类栏目数据
     * @param $data
     * @param $num
     */
    public function getLists($data,$num){
        $where = [
            'pid' => $data['pid'],
        ];
        $order = [
            'listorder' => 'desc',
            'id' => 'desc'
        ];
        $result = $this->where("status","<>",config("status.mysql.table_delete"))
            ->where($where)
            ->order($order)
            ->paginate($num);
        return $result;
    }

    /**
     * 获取子栏目总数
     * @param $pids
     */
    public function getChildLists($pids){
        if (empty($pids)){
            return [];
        }
        $where[] = [
            'pid','in',$pids
        ];
        $where[] = [
            'status','<>',config("status.mysql.table_delete")
        ];
        $result = $this->where($where)
            ->field(['pid','count(*) as count'])
            ->group('pid')
            ->select();
        return $result;
    }

    /**
     * 更新分类排序数值
     * @param $data
     */
    public function updateOrderList($data){
        $save_data = [
            'listorder' => $data['orderlist'],
            'update_time' => time()
        ];
        $result = $this->where('id',$data['id'])->save($save_data);
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
     * 删除分类信息
     * @param $id
     * @return bool
     */
    public function delCategory($id){
        $save_data = [
            'status' => config("status.mysql.table_delete"),
            'update_time' => time()
        ];
        $result = $this->where('id',$id)->save($save_data);
        return $result;
    }

    /**
     * 获取父类分类信息
     * @param $id
     */
    public function getParentById($id){
        $where = [
            'id' => $id
        ];
        $result = $this->where($where)->find();
        return $result;
    }

    /**
     * 更新分类
     * @param $data
     * @return bool
     */
    public function editSave($data){
        $where = [
            'id' => $data['id']
        ];
        $save_data = [
            'name' => $data['name'],
            'update_time' => time()
        ];
        $result = $this->where($where)->save($save_data);
        return $result;
    }

    /**
     * 根据获取分类数据
     * @param $pid
     * @param $field
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCategoryByPid($pid,$field){
        $where = [
            'pid' => $pid,
            'status' => config("status.mysql.table_normal")
        ];
        $order = [
            'listorder' => 'desc',
            'id' => 'desc'
        ];
        $result = $this->where($where)
            ->field($field)
            ->order($order)
            ->select();
        return $result;
    }

    /**
     * 根据ID获取首页栏目数据
     * @param $categoryId
     * @param $field
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGoodsRecommendById($categoryId,$field){
        $where = [
            'id' => $categoryId,
        ];
        $order = [
            'listorder' => 'desc',
            'id' => 'desc'
        ];
        $result = $this->where($where)
            ->field($field)
            ->order($order)
            ->find();
        return $result;
    }

    /**
     * 根据PID获取首页栏目数据
     * @param $categoryPid
     * @param $field
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGoodsRecommendByPid($categoryPid,$field){
        $where = [
            'pid' => $categoryPid,
            'status' => config("status.mysql.table_normal")
        ];
        $order = [
            'listorder' => 'asc',
            'id' => 'desc'
        ];
        $result = $this->where($where)
            ->field($field)
            ->order($order)
            ->select();
        return $result;
    }

    /**
     * 根据id获取数据
     * @param $id
     * @param string $field
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getCategoryId($id, $field = "*") {
        $where = [
            "id" => $id,
            "status" => config("status.mysql.table_normal")
        ];
        $result = $this->where($where)
            ->field($field)
            ->select();
        return $result;
    }

    /**
     * 根据二级栏目id获取三级栏目
     * @param $id
     * @param $field
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function sub($id,$field){
        $where = [
            'pid' => $id,
            'status' => config("status.mysql.table_normal")
        ];
        $order = [
            'listorder' => 'asc',
            'id' => 'desc'
        ];
        $result = $this->where($where)
            ->field($field)
            ->order($order)
            ->select();
        return $result;
    }
}