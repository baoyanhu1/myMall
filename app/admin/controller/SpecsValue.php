<?php


namespace app\admin\controller;
use app\admin\validate\SpecsValue as SpecsValueVil;
use app\common\business\SpecsValue as SpecsValueBus;
use think\Exception;

class SpecsValue extends AdminBase
{
    /**
     * 根据规格id获取规格属性
     * @return \think\response\Json
     */
    public function getBySpecsId(){
//        参数验证
        $specs_id = input("param.specs_id","","intval");
        $data = [
            'specs_id' => $specs_id
        ];
        $specsValueVil = new SpecsValueVil();
        $check = $specsValueVil->scene("getSpecs")->check($data);
        if (!$check){
            return show(config("status.error"),$specsValueVil->getError());
        }

//        业务逻辑
        try {
            $specsValueBus = new SpecsValueBus();
            $specsValues = $specsValueBus->getSpecsVaueBySpecsId($specs_id);
        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }
        return show(config("status.success"),'OK',$specsValues);

    }

    /**
     * 添加规格属性
     * @return \think\response\Json
     */
    public function save(){
//        参数验证
        $specs_id = input("param.specs_id","","intval");
        $name = input("param.name","","trim");
        $data = [
            'specs_id' => $specs_id,
            'name' => $name
        ];
        $specsValueVil = new SpecsValueVil();
        $check = $specsValueVil->scene("save")->check($data);
        if (!$check){
            return show(config("status.error"),$specsValueVil->getError());
        }
//        业务逻辑
        try {
            $specsValueBus = new SpecsValueBus();
            $specsValue = $specsValueBus->getSpecsVaueBySpecsIdName($specs_id,$name);
            if (!empty($specsValue)){
                return show(config("status.error"),'当前规格属性已存在');
            }
            $specsValues = $specsValueBus->save($data);
        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }
        if (!$specsValues){
            return show(config("status.error"),'添加规格属性失败');
        }
        return show(config("status.success"),'添加规格属性成功',$specsValues);
    }

    /**
     * 删除规格属性
     * @return \think\response\Json
     */
    public function del(){
        $id = input("param.id","","intval");
        $data = [
            'id' => $id
        ];
        $specsValueVil = new SpecsValueVil();
        $check = $specsValueVil->scene("del")->check($data);
        if (!$check){
            return show(config("status.error"),$specsValueVil->getError());
        }
        try {
            $specsValueBus = new SpecsValueBus();
            $result = $specsValueBus->delSpecsValue($id);
        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }
        if (!$result){
            return show(config("status.error"),'规格属性删除失败');
        }
        return show(config("status.success"),'规格属性删除成功');
    }
}