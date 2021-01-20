<?php
namespace app\common\business;


use app\common\lib\Tree;
use app\common\model\mysql\AdminRule;
use think\Exception;

class Rule
{
    public $sidebarObj;
    public function __construct()
    {
        $this->sidebarObj = new AdminRule();
    }

    /**
     * 侧边栏展示
     */
    public function show()
    {
        $info = $this->sidebarObj->getSidebarInfo();
        $tree = new Tree();
        $showTree = $tree->getTree($info,'id','child');
        $menu = [
            "clearInfo"=> [
                "clearUrl"=> "api/clear.json"
            ],
            "homeInfo"=> [
                "title"=> "首页 home",
                "icon"=> "fa fa-home",
                "href"=> "/admin/index/welcome"
            ],
            "logoInfo"=>[
                "title"=> "芸册后台管理",
                "href"=> ""
            ],
            "menuInfo"=> [
                "currency"=> [
                    "title"=> "商城管理",
                    "icon"=> "fa fa-address-book",
                    "child"=> $showTree
                ]
            ],
        ];
        return $menu;
    }
}