<?php
namespace app\admin\controller;

use app\admin\validate\AdminUser as AdminUserVil;
use app\common\lib\Show;
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
        $UserInfo = $modelObj->getUserInfo(3);
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
     * 提交
     */
    public function save()
    {
        $name = input("param.name","","trim");
        $password = input("param.password","","trim");
        $pass = input("param.pass","","trim");

        //参数验证
        $CategoryVil = new AdminUserVil();

        if ($password != $pass)
        {
            return Show::error();
        }
        $modelObj = new AdminUserBus;
        $setSave = $modelObj->setSave($name,$password,$pass);
        return $pass;
    }
}