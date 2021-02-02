<?php


namespace app\admin\controller;

use app\common\business\AdminRule as AdminRuleBus;
use think\facade\View;

class Index extends AdminBase
{
    /**
     * 渲染后台视图
     * @return string
     */
    public function index(){
        return View::fetch();
    }

    public function getMenu(){
        $obj = new AdminRuleBus();
        //当前登录用户
        $isUser =  session(config("admin.admin_user"))["id"];
        $sidebar = $obj->Menu($isUser);
        return $sidebar;
    }

    /**
     * 渲染后台首页视图
     * @return string
     */
    public function welcome(){
        return View::fetch();
    }
}