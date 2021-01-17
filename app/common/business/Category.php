<?php


namespace app\common\business;
use app\common\model\mysql\Category as CategoryModel;
use think\Exception;

class Category
{
    public $model;
    public function __construct()
    {
        $this->model = new CategoryModel();
    }

    /**
     * 添加商品分类
     * @param $data
     */
    public function add($data){
        $this->model->startTrans();
        try {
            $data['status'] = config("status.mysql.table_normal");
            $name = $data['name'];
            //获取他上级分类的path
            $res = $this->model->getCategoryId($data['pid']);
            $res = $res->toArray();


            $categoryInfo = $this->model->getCategoryInfo($data);
            if ($categoryInfo && $categoryInfo['status'] != config("status.mysql.table_delete")){
                throw new Exception("分类已存在");
            }
            //首先保存分类名
            $this->model->save($data);
            //获取最后一个新增ID return $this->model->getLastInsID();
            $id = $this->model->id;
            if (!empty($res)){
                $path = $res[0]["path"];
                $path = $path.",".$id;
                $data = [
                    "path" => $path
                ];
            }else{
                $path = $id;
                $data = [
                    "path" => $path
                ];
            }
            //通过拼接path，再做一次更新
            $this->model->updateById($id, $data);

            //事务提交
            $this->model->commit();
            return $id;
        } catch (\think\Exception $e) {
            //事务回滚
            $this->model->rollback();
            throw new \think\Exception("服务内部异常");
        }
    }

    /**
     * 查询所有分类数据
     */
    public function getCategorys(){
        $fileds = "id,name,pid";
        try {
            $categorys = $this->model->getCategorys($fileds);
        }catch (Exception $e){
            $categorys = [];
        }
        return $categorys;
    }

    /**
     * 前端无限分类展示
     * @return array
     */
    public function getAllCategorys(){
        $fileds = "id as category_id,name,pid";
        try {
            $categorys = $this->model->getCategorys($fileds);
        }catch (Exception $e){
            $categorys = [];
        }
        return $categorys;
    }

    /**
     * 获取分类栏目数据
     * @param $data
     * @param $num
     */
    public function getLists($data,$num){
        $categoryList = $this->model->getLists($data,$num);
        if (!$categoryList){
            return [];
        }
        $result = $categoryList->toArray();
        $result['render'] = $categoryList->render();

//        获取每一个分类下子分类的总数
//        先获取当前所有的父类id
        $pids = array_column($result['data'],'id');
        $childs = $this->model->getChildLists($pids);
        if (empty($childs)){
            $childs = [];
        }else{
            $childs = $childs->toArray();
        }
        $idCounts = [];
        foreach ($childs as $item){
            $idCounts[$item['pid']] = $item['count'];
        }
//        给返回的数据新加childcount Key
        foreach ($result['data'] as $k=>$item){
            $result['data'][$k]['childCount'] = $idCounts[$item['id']] ?? 0;
        }

        return $result;
    }

    /**
     * 根据id获取数据
     * @param $id
     */
    public function getById($id){
        $categoryInfo = $this->model->find($id);
        if (!$categoryInfo){
            return [];
        }
        return $categoryInfo;
    }

    /**
     * 更新分类排序
     * @param $data
     */
    public function updateOrderList($data){
        $categoryInfo = $this->getById($data['id']);
        if (empty($categoryInfo)){
            throw new Exception('不存在当前分类信息');
        }
        try {
            $result = $this->model->updateOrderList($data);
        }catch (Exception $e){
            throw new Exception('系统错误');
        }

        return $result;
    }

    /**
     * 更改分类状态
     * @param $data
     */
    public function changeStatus($data){
        $categoryInfo = $this->getById($data['id']);
        if (empty($categoryInfo)){
            throw new Exception('不存在当前分类信息');
        }
        if ($categoryInfo['status'] == $data['status']){
            throw new Exception('请勿多次修改相同的分类状态');
        }
        try {
            $result = $this->model->changeStatus($data);
        }catch (Exception $e){
            throw new Exception('系统错误');
        }

        return $result;
    }

    /**
     * 删除分类
     * @param $id
     */
    public function delCategory($id){
        $categoryInfo = $this->getById($id);
        if (empty($categoryInfo)){
            throw new Exception('不存在当前分类信息');
        }
        try {
            $result = $this->model->delCategory($id);
        }catch (Exception $e){
            throw new Exception('系统错误');
        }
        return $result;
    }

    /**
     * 获取父类分类信息
     * @param $id
     */
    public function getParentById($id){
        $categoryInfo = $this->getById($id);
        if (empty($categoryInfo)){
            throw new Exception('不存父类分类信息');
        }
        $parentCategory = $this->model->getParentById($id);
        if (!$parentCategory){
            return [];
        }
        return $parentCategory;
    }

    /**
     * 更新分类
     * @param $data
     * @return bool
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editSave($data){
        $categoryInfo = $this->getById($data['id']);
        if (empty($categoryInfo)){
            throw new Exception('不存在当前分类信息');
        }

        $categoryInfo = $this->model->getCategoryInfo($data);
        if ($categoryInfo && $categoryInfo['status'] != config("status.mysql.table_delete")){
            throw new Exception("分类已存在");
        }
        try {
            $result = $this->model->editSave($data);
        }catch (Exception $e){
            throw new Exception("系统错误");
        }
        return $result;
    }

    /**
     * 根据pid获取分类数据
     * @param int $pid
     * @return array
     */
    public function getCategoryByPid($pid = 0){
        $field = "id,name,pid";
        try {
            $result = $this->model->getCategoryByPid($pid,$field);
        }catch (Exception $e){
            return [];
        }
        return $result->toArray();
    }

    /**
     * 根据id获取首页栏目数据
     * @param $id
     */
    public function getGoodsRecommendById($categoryId){
        if (!$categoryId){
            return [];
        }
        $field = "id as category_id,name,icon";
        try {
            $result = $this->model->getGoodsRecommendById($categoryId,$field);
        }catch (Exception $e){
            return [];
        }
        return $result->toArray();
    }

    /**
     * 根据PID获取首页栏目数据
     * @param $categoryPid
     * @return array
     */
    public function getGoodsRecommendByPid($categoryPid){
        if (!$categoryPid){
            return [];
        }
        $field = "id as category_id,name";
        try {
            $result = $this->model->getGoodsRecommendByPid($categoryPid,$field);
        }catch (Exception $e){
            return [];
        }
        return $result->toArray();
    }

    /**
     * 前端分类搜索
     * @param $id
     * @return array
     */
    public function search($id){
        try {
            $result = [];
            $res = $this->model->getCategoryId($id)->toArray();
            // 获取path值
            $categoryPath = explode(",", $res[0]['path']);
            //获取一级分类名称
            $categoryOne = array_slice($categoryPath, 0, 1);

            $result["name"] = $this->model
                ->getCategoryId($categoryOne[0], "name")
                ->toArray()[0]['name'];

            //获取定位点focus_ids
            $result["focus_ids"] = array_slice($categoryPath, 1);

            foreach ($result['focus_ids'] as $key => $value) {
                $result['focus_ids'][$key] = intval($value);
            }
            //获取子分类list
            array_pop($categoryPath);
            if (count($categoryPath) == 0) { //如果是一级分类则获取他下面的二级分类
                $result['list'][0] = $this->model
                    ->getCategoryByPid($categoryOne, "id, name")
                    ->toArray();
            } else { //如果是二级和三级分类，则获取同pid的所有分类
                foreach ($categoryPath as $k => $v) {
                    $result['list'][$k] = $this->model
                        ->getCategoryByPid($v, "id, name")
                        ->toArray();
                }
            }
        } catch (\Exception $e) {
            return [];
        }
        return $result;
    }

    /**
     * 根据二级栏目id获取三级栏目
     * @param $id
     * @return array
     */
    public function sub($id){
        $field = "id,name";
        try {
            $result = $this->model->sub($id,$field);
        }catch (Exception $e){
            return [];
        }
        return $result->toArray();
    }
}