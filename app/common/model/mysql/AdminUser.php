<?php


namespace app\common\model\mysql;


use think\Model;

class AdminUser extends BaseModel
{
    /**
     * 根据用户名获取后台管理用户信息
     * @param $username
     * @return array|bool|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminUserByUserName($username){
        if (empty($username)){
            return false;
        }
        $where = [
            "username" => $username
        ];
        $result = $this->where($where)->find();
        return $result;
    }

    /**
     * 根据主键id更新用户信息
     * @param $id
     * @param $data
     * @return bool
     */
    public function updateById($id,$data){
        $id = intval($id);
        if (empty($id) || empty($data) || !is_array($data)){
            return false;
        }
        $where = [
            "id" => $id
        ];
        $result = $this->where($where)->save($data);
        return $result;
    }

    /**
     * 用户信息
     * @param string $status
     * @param $num
     * @return \think\Paginator
     * @throws \think\db\exception\DbException
     */
    public function getUserInfo($num,$status = "")
    {
        $statusNormal = !empty($status) ? $status : config("status.mysql.table_normal");
        $statusPending = !empty($status) ? $status : config("status.mysql.table_pending");
        $where = [
            'status' => [$statusNormal,$statusPending]
        ];
        $order = [
            'id' => 'ASC'
        ];
        return $this->where($where)
            ->order($order)
            ->paginate($num);
    }

    /**
     * 提交用户信息
     * @param $username
     * @param $password
     * @param $isUser
     */
    public function setSave($username,$password,$isUser)
    {
        $data = [
            "username" => $username,
            "password" => $password,
            "status" => config('status.mysql.table_normal'),
            "create_time" => time(),
            "update_time" => time(),
            "last_login_time" => "",
            "last_login_ip" => "",
            "operate_user" => $isUser
        ];

        return $this->insert($data);
    }

}