<?php
namespace app\common\business;

use think\Exception;

class AdminRoleRule extends BusBase
{
    public $model;
    public function __construct()
    {
        $this->model = new \app\common\model\mysql\AdminRoleRule();
    }
    /**
     * 根据role_id查询角色下权限
     * @param $id
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getRoleRule($id)
    {
        try {
            return $this->model->getRoleRule($id)->toArray();
        }catch (Exception $e){
            return [];
        }

    }
}