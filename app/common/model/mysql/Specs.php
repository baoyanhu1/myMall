<?php


namespace app\common\model\mysql;


use think\Model;

class Specs extends Model
{
    //    写入数据时自动写入创建时间和更新时间
    protected $autoWriteTimestamp = true;

    /**
     * 获取所有规格
     * @param $field
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAllSpecs($field = "*"){
        $where = [
            'status' => config('status.mysql.table_normal')
        ];
        $result = $this->where($where)
            ->field($field)
            ->select();
        return $result;
    }
}