<?php
namespace app\common\business;


use app\common\lib\Tree;
use app\common\model\mysql\AdminRule as AdminRuleModel;
use think\Exception;

class AdminRule extends BusBase
{
    public $model;
    public function __construct()
    {
        $this->model = new AdminRuleModel();
    }

    /**
     * 侧边栏展示
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function Menu()
    {
        $fidle = 'id,title,pid,icon,href,target';
        try {
            $info = $this->model->getAllPermissions($fidle);
        }catch (Exception $e){
            return [];
        }
        $arrayInfo = $info->toArray();
        $tree = new Tree();
        $showTree = $tree->getTree($arrayInfo,'id','child');
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

    /**
     * 展示角色下权限名称
     * @param $data
     * @return array
     */
    public function ArrangementAuthority($data)
    {
        $arr[] = $data['rule_ids'];
        try {
            return $this->model->ArrangementAuthority($arr);
        }catch (Exception $e){
            return [];
        }
    }

    /**
     * 展示所有title
     * @return array
     */
    public function getTitleAll()
    {
        try {
            return $this->model->getTitleAll()->toArray();
        }catch (Exception $e){
            return [];
        }
    }
}