<?php


namespace app\common\model\mysql;


class ReceiptAddress extends BaseModel
{
    /**
     * 获取用户收货地址
     * @param $userId
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getReceiptAddress($userId){
        $where = [
            "user_id" => $userId,
            "status" => config("status.mysql.table_normal")
        ];
        $result = $this->where($where)->select();
        return $result;
    }

}