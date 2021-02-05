<?php
namespace app\common\model\mysql;


use think\Model;

class WxUser extends Model
{
    /**
     * 更新微信user信息
     * @param $openid
     * @param $data
     * @return WxUser
     */
    public function updateWxUser($openid,$data)
    {
        $where = [
          'openid' => $openid
        ];
        return $this->where($where)->update($data);
    }

    /**
     * 写入微信User信息
     * @param $data
     * @return bool
     */
    public function saveWxUser($data)
    {
        return $this->insert($data);
    }

    /**
     * 验证是否有该用户
     * @param $openid
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function verificationInfo($openid)
    {
        $where = [
            "openid" => $openid
        ];
        return $this->where($where)->find();
    }
}