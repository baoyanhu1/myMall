<?php


namespace app\common\model\mysql;


use think\Model;

class SpecsValue extends Model
{
    //    写入数据时自动写入创建时间和更新时间
    protected $autoWriteTimestamp = true;

    /**
     * 根据规格id获取规格属性
     * @param $specs_id
     * @param $field
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSpecsVaueBySpecsId($specs_id,$field){
        $where = [
            'specs_id' => $specs_id,
            'status' => config("status.mysql.table_normal")
        ];
        $result = $this->where($where)
            ->field($field)
            ->select();
        return $result;
    }

    /**
     * 根据规格id和规格属性获取信息
     * @param $specs_id
     * @param $name
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSpecsVaueBySpecsIdName($specs_id,$name){
        $where = [
            'specs_id' => $specs_id,
            'name' => $name,
            'status' => config("status.mysql.table_normal")
        ];
        $result = $this->where($where)->find();
        return $result;
    }

    /**
     * 根据规格属性id获取数据
     * @param $id
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getById($id){
        $where = [
            'id' => $id,
            'status' => config("status.mysql.table_normal")
        ];
        $result = $this->where($where)->find();
        return $result;
    }

    /**
     * 删除规格属性
     * @param $id
     * @return bool
     */
    public function delSpecsValue($id){
        $where = [
            'id' => $id
        ];
        $data = [
            'status' => config("status.mysql.table_delete"),
            'update_time' => time()
        ];
        $result = $this->where($where)->save($data);
        return $result;
    }
}