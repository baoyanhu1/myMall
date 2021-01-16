<?php


namespace app\admin\controller;


use think\facade\View;
use app\common\business\Specs as SpecsBus;

class Specs extends AdminBase
{
    /**
     * 返回所有规格渲染规格属性页
     * @return string
     */
    public function dialog(){
        $specsBus = new SpecsBus();
        $specs = $specsBus->getAllSpecs();
        return View::fetch("",[
            "specs" => json_encode($specs)
        ]);
    }
}