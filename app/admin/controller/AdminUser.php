<?php
namespace app\admin\controller;

use app\admin\validate\AdminUser as AdminUserVil;
use think\Exception;
use think\facade\View;
use app\common\business\AdminUser as AdminUserBus;

class AdminUser extends AdminBase
{
    /**
     * 用户信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        $modelObj = new AdminUserBus;
        $UserInfo = $modelObj->getUserInfo(10);
        return View::fetch("",[
            "UserInfo" => $UserInfo
        ]);
    }

    /**
     * 添加账号弹出层
     */
    public function add()
    {
        return View::fetch();
    }

    /**
     * 提交管理员信息
     * @return \think\response\Json|void
     */
    public function save()
    {
        $username = input("param.username","","trim");
        $password = input("param.password","","trim");
        $password_confirm = input("param.password_confirm","","trim");

        $data = [
            "username" => $username,
            "password" => $password,
            "password_confirm" => $password_confirm
        ];

        //参数验证
        $AdminUserVil = new AdminUserVil();
        $check = $AdminUserVil->scene('user')->check($data);
        if (!$check){
            return show(config("status.error"),$AdminUserVil->getError());
        }
        //当前登录用户
        $isUser =  session(config("admin.admin_user"))["username"];
        //        到业务逻辑
        try {
            $modelObj = new AdminUserBus;
            //重复查询
            if (!empty($modelObj->getAdminUserByUserName($username))) {
                return show(config("status.error"),config("message.AdminUser.DuplicateData"));
            }

            $save = $modelObj->setSave($data,$isUser);
            if (!$save){
                return show(config("status.error"),config("message.AdminUser.AddFailed"));
            }
        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }

        return show(config("status.success"),config("message.AdminUser.AddedSuccessfully"));
    }

    /**
     * 更改管理员状态
     * @return \think\response\Json
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
        $AdminUserVil = new AdminUserVil();
        $check = $AdminUserVil->scene('status')->check($data);
        if (!$check){
            return show(config("status.error"),$AdminUserVil->getError());
        }

        //修改状态
        try {
            $modelObj = new AdminUserBus();
            $result = $modelObj->changeStatus($data["id"],$status);
        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }
        if (!$result){
            return show(config("status.error"),config("message.AdminUser.StatusModificationFailed"));
        }
        return show(config("status.success"),config("message.AdminUser.StatusModifiedSuccessfully"));
    }

    /**
     * 软删除管理员
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
        $AdminUserVil = new AdminUserVil();
        $check = $AdminUserVil->scene('status')->check($data);
        if (!$check){
            return show(config("status.error"),$AdminUserVil->getError());
        }

        // 删除分类
        try {
            $modelObj = new AdminUserBus();
            $result = $modelObj->changeStatus($data["id"],$status);
        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }
        if (!$result){
            return show(config("status.error"),config("message.AdminUser.AdministratorDeleteFailed"));
        }
        return show(config("status.success"),config("message.AdminUser.AdministratorDeletedSuccessfully"));
    }

    /**
     * 编辑管理员信息弹出层
     * @return string|\think\response\Json
     */
    public function edit()
    {
        $id = input("id","","intval");
        try {
            $modelObj = new AdminUserBus();
            $result = $modelObj->getAdminUserById($id);
        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }

        return View::fetch("",[
            "username" => $result['username'],
            "id" => $id
        ]);
    }

    /**
     * 编辑管理员信息
     * @return \think\response\Json
     */
    public function editSave()
    {
        $id = input("param.id","","trim");
        $password = input("param.password","","trim");
        $password_confirm = input("param.password_confirm","","trim");

        $data = [
            "id" => $id,
            "password" => $password,
            "password_confirm" => $password_confirm
        ];


        //参数验证
        $AdminUserVil = new AdminUserVil();
        $check = $AdminUserVil->scene('password')->check($data);
        if (!$check){
            return show(config("status.error"),$AdminUserVil->getError());
        }
        //当前登录用户
        $isUser =  session(config("admin.admin_user"))["username"];
        //        到业务逻辑
        try {
            $modelObj = new AdminUserBus;
            $save = $modelObj->edit($data['id'],$data['password'],$isUser);
            if (!$save){
                return show(config("status.error"),config("message.AdminUser.EditFailed"));
            }
        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }

        return show(config("status.success"),config("message.AdminUser.EditedSuccessfully"));
    }

    /**
     * 管理员权限弹出层
     * @return string
     */
    public function power()
    {

        return View::fetch();
    }

    /**
     * 更改管理员权限
     */
    public function powerSave()
    {

    }

}