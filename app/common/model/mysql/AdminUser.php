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
        $data['update_time'] = time();
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
     * @param $data
     * @return int|string
     */
    public function setSave($data)
    {
        return $this->insert($data);
    }

    /**
     * 根据主键ID 查询管理员名称
     * @param $id
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminUserById($id)
    {
        $where = [
          "id" => $id
        ];
        return $this->where($where)->find();
    }
}