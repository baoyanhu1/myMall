<?php


namespace app\common\business;


use think\Exception;

class BusBase
{
    /**
     * 保存数据（已保存：规格属性）
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function save($data){
        $data['status'] = config("status.mysql.table_normal");
        try {
            $result = $this->model->save($data);
        }catch (Exception $e){
            throw new Exception("系统异常");
        }
        return $this->model->id;
    }
}