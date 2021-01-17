<?php


namespace app\api\controller\mall;


use app\api\controller\ApiBase;
use app\common\business\Goods as GoodsBus;
use app\common\lib\Show;

class Lists extends ApiBase
{
    /**
     * 按分类查询商品数据/按关键字搜索商品
     * @return \think\response\Json
     */
    public function index(){
//        按分类ID查询商品
        $category_id = input("param.category_id",'',"intval");
        $page_size = input("param.page_size",10,"intval");
        //分类商品页按价格/销量排序
        $field = input("param.field","listorder","trim");
        $order = input("param.order",2,"intval");
//        前端商品搜索页按商品名称搜索关键词
        $keywords = input("param.keyword","","trim");
        if ($order == 2){
            $order = "desc";
        }else{
            $order = "asc";
        }
        $order = [$field => $order];
        $goodsBus = new GoodsBus();
        $result = $goodsBus->getGoodsLists($category_id,$page_size,$order,$keywords);
        return Show::success($result);
    }
}