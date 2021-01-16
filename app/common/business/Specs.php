<?php


namespace app\common\business;
use app\common\model\mysql\Specs as SpecsModel;
use think\Exception;

class Specs
{
    public $model;
    public function __construct()
    {
        $this->model = new SpecsModel();
    }

    /**
     * 获取所有规格
     * @return array
     * @throws Exception
     */
    public function getAllSpecs(){
        $field = "id,name";
        try {
            $result = $this->model->getAllSpecs($field);
        }catch (Exception $e){
            return [];
        }
        return $result->toArray();

    }
}