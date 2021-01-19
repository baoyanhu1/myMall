<?php


namespace app\common\business;
use app\common\model\mysql\SpecsValue as SpecsValueModel;
use think\Exception;

class SpecsValue extends BusBase
{
    public $model;
    public function __construct()
    {
        $this->model = new SpecsValueModel();
    }

    /**
     * 根据规格id获取规格属性
     * @param $specs_id
     * @return array
     * @throws Exception
     */
    public function getSpecsVaueBySpecsId($specs_id){
        $field = "id,name";
        try {
            $result = $this->model->getSpecsVaueBySpecsId($specs_id,$field);
        }catch (Exception $e){
            throw new Exception("系统异常");
        }
        return $result->toArray();
    }

    /**
     * 根据规格id和规格属性获取信息
     * @param $specs_id
     * @param $name
     * @return array
     * @throws Exception
     */
    public function getSpecsVaueBySpecsIdName($specs_id,$name){
        try {
            $result = $this->model->getSpecsVaueBySpecsIdName($specs_id,$name);
        }catch (Exception $e){
            throw new Exception("系统异常");
        }
        return $result;
    }

    /**
     * 根据规格属性id获取数据
     * @param $id
     * @return array|\think\Model|null
     * @throws Exception
     */
    public function getById($id){
        try {
            $result = $this->model->getById($id);
        }catch (Exception $e){
            throw new Exception("系统异常");
        }
        return $result;
    }

    /**
     * 删除规格属性
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function delSpecsValue($id){
        $specsValue = $this->getById($id);
        if (!$specsValue){
            throw new Exception("当前规格属性不存在");
        }
        try {
            $result = $this->model->delSpecsValue($id);
        }catch (Exception $e){
            throw new Exception("系统异常");
        }
        return $result;

    }

    /**
     * 根据skuID获取规格名称和规格属性
     * @param $gids
     * @param $flagValue
     * @return array
     */
    public function getSpecsValueById($gids,$flagValue){
        $specsValues = array_keys($gids);
        foreach ($specsValues as $specsValue){
            $specsValue = explode(",",$specsValue);
            foreach ($specsValue as $k => $v){
//                循环将规则属性id分组
                $newSpecsValue[$k][] = $v;
//                所有规格属性Id
                $specsValueIds[] = $v;
            }
        }
//        去除重复的规格属性
        $specsValueIds = array_unique($specsValueIds);
        $specsValue = $this->getNormalInIds($specsValueIds);

        $flagValue = explode(",",$flagValue);
        $result = [];
        foreach ($newSpecsValue as $k => $value){
            $specsValueUnique = array_unique($value);
            $list = [];
//            封装规格属性LIST
            foreach ($specsValueUnique as $v){
                $list[] = [
                    "id" => $v,
                    "name" => $specsValue[$v]['name'],
                    "flag" => in_array($v,$flagValue) ? 1 :0
                ];
            }
//            封装最终的sku数据
            $result[$k] = [
                "name" => $specsValue[$value[0]]['specs_name'],
                "list" => $list
            ];
        }

        return $result;
    }

    /**
     * 按规格属性ID获取规格名称和规格属性
     * @param $ids
     * @return array
     * @throws Exception
     */
    public function getNormalInIds($ids){
        if (!$ids){
            return [];
        }
        try {
//            获取当前商品所有规格属性
            $result = $this->model->getNormalInIds($ids);
        }catch (Exception $e){
            return [];
        }
        $result = $result->toArray();
        if (!$result){
            return [];
        }
        $specsBus = new Specs();
//        获取所有规格
        $specs = $specsBus->getAllSpecs();
        $specsName = array_column($specs,"name","id");
//        返回商品规格属性名称和商品规格名称
        $res = [];
        foreach ($result as $value){
            $res[$value['id']] = [
                "name" => $value["name"],
                "specs_name" => $specsName[$value["specs_id"]] ?? "",
                ];
        }
        return $res;
    }

    /**
     * 根据商品属性ID获取商品规格名称及商品属性名称并哦您接文案返回
     * @param $specsValuIds
     * @return array
     * @throws Exception
     */
    public function dealSpecsValues($specsValuIds){
        $ids = implode(",",$specsValuIds);
        $ids = array_unique(explode(",",$ids));
        $result = $this->getNormalInIds($ids);
        if (!$result){
            return [];
        }
        $res = [];
        foreach ($specsValuIds as $k => $specs){
            $specs = explode(",",$specs);
            $specsStr = [];
            foreach ($specs as $spec){
                $specsStr[] = $result[$spec]['specs_name'].":".$result[$spec]['name'];
            }
            $res[$k] = implode("  ",$specsStr);
        }
        return $res;
    }

}