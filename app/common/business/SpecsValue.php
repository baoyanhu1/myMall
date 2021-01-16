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

}