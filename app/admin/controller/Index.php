<?php


namespace app\admin\controller;

use app\common\business\Rule;
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
                    "child"=> [
                        [
                            "title"=> "分类管理",
                            "href"=> "/admin/category",
                            "icon"=> "fa fa-th-large",
                            "target"=> "_self"
                        ],
                        [
                            "title"=> "商品管理",
                            "href"=> "",
                            "icon"=> "fa fa-calendar",
                            "target"=> "_self",
                            "child"=> [
                                [
                                    "title"=> "商品列表",
                                    "href"=> "/admin/goods",
                                    "icon"=> "fa fa-list-alt",
                                    "target"=> "_self"
                                ],
                                [
                                    "title"=> "商品添加",
                                    "href"=> "/admin/goods/add",
                                    "icon"=> "fa fa-navicon",
                                    "target"=> "_self"
                                ]
                            ]
                        ]

                    ]
                ]
            ],


        ];
        $obj = new Rule();
        $sidebar = $obj->show();
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