<?php
namespace app\admin\controller;

use app\admin\validate\AdminRole as AdminRoleVil;
use think\Exception;
use think\facade\View;
use app\common\business\AdminRole as AdminRoleBus;

class AdminRole extends AdminBase
{
    /**
     * 角色信息
     * @return string
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        $modelObj = new AdminRoleBus;
        $roleInfo  = $modelObj->getRoleInfo(10);
        return View::fetch("",[
            "roleInfo" => $roleInfo
        ]);
    }

    /**
     * 添加角色弹出层
     * @return string
     */
    public function add()
    {
        return View::fetch();
    }

    /**
     * 提交并保存角色信息
     */
    public function save()
    {
        $name = input("param.name","","trim");
        $data = [
            'name' => $name
        ];
        //参数验证
        $AdminRoleVil = new AdminRoleVil();
        $check = $AdminRoleVil->scene('role')->check($data);
        if (!$check){
            return show(config("status.error"),$AdminRoleVil->getError());
        }
        //当前登录用户
        $isUser =  session(config("admin.admin_user"))["username"];
        //        到业务逻辑
        try {
            $modelObj = new AdminRoleBus;
            //重复查询
            if (!empty($modelObj->getAdminRoleByName($name))) {
                return show(config("status.error"),config("message.AdminRole.DuplicateRoleName"));
            }

            $save = $modelObj->setSave($data,$isUser);
            if (!$save){
                return show(config("status.error"),config("message.AdminRole.FailedToAddRole"));
            }
        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }

        return show(config("status.success"),config("message.AdminRole.RoleAddedSuccessfully"));
    }

    /**
     * 软删除角色
     * @return \think\response\Json
     */
    public function del()
    {
        $id = input("id","","intval");
        $status = input("status","","intval");
        $data = [
            'id' => $id,
            'status' => $status
        ];
        $status = [
            'status' => $status
        ];

        //        验证参数
        $AdminRoleVil = new AdminRoleVil();
        $check = $AdminRoleVil->scene('status')->check($data);
        if (!$check){
            return show(config("status.error"),$AdminRoleVil->getError());
        }

        // 删除分类
        try {
            $modelObj = new AdminRoleBus();
            $result = $modelObj->changeStatus($data["id"],$status);
        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }
        if (!$result){
            return show(config("status.error"),config("message.AdminRole.RoleDeletionFailed"));
        }
        return show(config("status.success"),config("message.AdminRole.RoleDeletedSuccessfully"));
    }

    /**
     * 更改角色状态
     */
    public function changeStatus()
    {
        $id = input("id","","intval");
        $status = input("status","","intval");
        $data = [
            'id' => $id,
            'status' => $status
        ];

        $status = [
            'status' => $status,
            //当前登录用户
            "operate_user" => session(config("admin.admin_user"))["username"]
        ];

        //参数验证
        $AdminRoleVil = new AdminRoleVil();
        $check = $AdminRoleVil->scene('status')->check($data);
        if (!$check){
            return show(config("status.error"),$AdminRoleVil->getError());
        }

        //修改状态
        try {
            $modelObj = new AdminRoleBus();
            $result = $modelObj->changeStatus($data["id"],$status);
        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }
        if (!$result){
            return show(config("status.error"),config("message.AdminUser.StatusModificationFailed"));
        }
        return show(config("status.success"),config("message.AdminUser.StatusModifiedSuccessfully"));
    }
}