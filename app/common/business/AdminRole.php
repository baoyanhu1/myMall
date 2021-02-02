<?php
namespace app\common\business;

use app\common\lib\Tree;
use think\Exception;

class AdminRole extends BusBase
{
    public $model;
    public function __construct()
    {
        $this->model = new \app\common\model\mysql\AdminRole();
    }



    /**
     * 获取角色信息
     * @param $num
     * @return array
     * @throws \think\db\exception\DbException
     */
    public function getRoleInfo($num)
    {
        try {
            $info = $this->model->getRoleInfo($num);
        }catch (Exception $e){
            return [];
        }
        return $info->toArray();
    }

    /**
     * 通过角色名称查询单条数据
     * @param $name
     * @return array|false|\think\Model|null
     */
    public function getAdminRoleByName($name)
    {
        try {
            return $this->model->getAdminRoleByName($name);
        }catch (Exception $e){
            return [];
        }

    }

    /**
     * 角色信息提交并保存
     * @param $data
     * @param $isUser
     * @return array|int|string
     */
    public function setSave($data,$isUser)
    {
        $data = [
            "name" => $data['name'],
            "create_time" => time(),
            "update_time" => time(),
            "operate_user" => $isUser,
            "status" => config('status.mysql.table_normal')
        ];

        try {
            return $this->model->setSave($data);
        }catch (Exception $e){
            return [];
        }
    }

    /**
     * 更改角色状态
     * @param $data
     * @param $id
     */
    public function changeStatus($id,$data)
    {
        try {
            return $this->model->changeStatus($id,$data);
        }catch (Exception $e){
            return [];
        }
    }

    /**
     * 通过ID获取角色名称
     * @param $id
     * @return array
     */
    public function getRoleNameIsId($id)
    {
        try {
            return $this->model->getRoleNameIsId($id);
        }catch (Exception $e){
            return [];
        }
    }

    /**
     * 通过ID排除当前管理角色
     * @param $id
     * @return array|\think\Collection
     */
    public function removeRoleNameById($id)
    {
        try {
            return $this->model->removeRoleNameById($id);
        }catch (Exception $e){
            return [];
        }
    }

    /**
     * 获取当前角色下权限信息
     * @param $id
     * @return Model|array|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getRoleRuleInfoById($id)
    {
        $RoleRule = new AdminRoleRule();
        return $RoleRule->getRoleRule($id);
    }

    /**
     * 展示角色下权限名称
     * @param $data
     * @return array
     */
    public function ArrangementAuthority($data)
    {
        $Rule = new AdminRule();
        return $Rule->ArrangementAuthority($data);
    }

    /**
     * 展示所有title
     * @return array
     */
    public function getTitleAll()
    {
        $Rule = new AdminRule();
        $tree = new Tree();
        return $tree->getTree($Rule->getTitleAll(),'id','child');
    }

    /**
     * 保存角色权限
     * @param $empty
     * @param $data
     * @param $isUser
     * @return array
     */
    public function roleRuleSave($empty,$data,$isUser)
    {
        $RoleRule = new AdminRoleRule();
        return $RoleRule->roleRuleSave($empty,$data,$isUser);
    }
}