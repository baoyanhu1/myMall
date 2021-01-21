<?php
namespace app\admin\controller;

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
}