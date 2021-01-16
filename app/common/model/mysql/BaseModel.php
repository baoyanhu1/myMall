<?php


namespace app\common\model\mysql;


use think\Model;

class BaseModel extends Model
{
    //    写入数据时自动写入创建时间和更新时间
    protected $autoWriteTimestamp = true;

    /**
     * 根据主键更新数据
     * @param $id
     * @param $data
     * @return bool
     */
    public function updateById($id,$data){
        $data['update_time'] = time();
        $where = [
            "id" => $id
        ];
        $result = $this->where($where)->save($data);
        return $result;
    }
}