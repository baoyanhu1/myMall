<?php


namespace app\admin\controller;

use app\common\lib\Oss;
use think\Exception;
use think\facade\Filesystem;
use think\facade\Validate;

class Image extends AdminBase
{
    /**
     * OSS图片上传
     * @return \think\response\Json
     */
    public function upload()
    {
        if (!$this->request->isPost()) {
            return show(config("status.error"), "请求方式错误");
        }
        $file = request()->file("file");
//        验证图片信息
        if (!Validate::fileSize($file, 1 * 1024 * 1024)) {
            return show(config("status.error"), "图片大小不能超过1MB");
        }
        if (!Validate::fileExt($file, 'jpeg,jpg,png,gif')) {
            return show(config("status.error"), "图片类型错误");
        }
        $ossObj = new Oss();
        $image = $_FILES['file'];
        try {
            $result = $ossObj->upload($image);
        }catch (Exception $e){
            return show(config("status.error"),"上传失败");
        }
        if ($result['status'] == 0){
            return show(config("status.error"),"上传失败");
        }
        $imageUrl = [
            "image" => $result['url']
        ];
        return show(config("status.success"),"上传成功",$imageUrl);
    }

    /**
     * layui上传图片到OSS
     * @return \think\response\Json
     */
    public function layUpload()
    {
        if (!$this->request->isPost()) {
            return show(config("status.error"), "请求方式错误");
        }
        $file = request()->file("file");
//        验证图片信息
        if (!Validate::fileSize($file, 1 * 1024 * 1024)) {
            return show(config("status.error"), "图片大小不能超过1MB");
        }
        if (!Validate::fileExt($file, 'jpeg,jpg,png,gif')) {
            return show(config("status.error"), "图片类型错误");
        }
        $ossObj = new Oss();
        $image = $_FILES['file'];
        try {
            $result = $ossObj->upload($image);
        }catch (Exception $e){
            return show(config("status.error"),"上传失败");
        }
        if ($result['status'] == 0){
            return show(config("status.error"),"上传失败");
        }
        $imageUrl = $result['url'];
        return imageShow(config("status.error"), "上传成功", $imageUrl);
    }

    //TP6上传图片
//    public function upload(){
//        if (!$this->request->isPost()){
//            return show(config("status.error"),"请求方式错误");
//        }
//        $file = request()->file("file");
////        验证图片信息
//        if (!Validate::fileSize($file,1*1024*1024)){
//            return show(config("status.error"),"图片大小不能超过1MB");
//        }
//        if (!Validate::fileExt($file,'jpeg,jpg,png,gif')){
//            return show(config("status.error"),"图片类型错误");
//        }
////        tp6自定义上传文件
//        $filename = Filesystem::disk("public")->putFile("image",$file);
//        if (!$filename){
//            return show(config("status.error"),"图片上传失败");
//        }
//        $imageUrl = [
//            "image" => "/upload/".$filename
//        ];
//        return show(config("status.success"),"上传成功",$imageUrl);
//
//    }

    /**
     * layui富文本编辑器上传图片
     * @return \think\response\Json
     */
//    public function layUpload()
//    {
//        if (!$this->request->isPost()) {
//            return show(config("status.error"), "请求方式错误");
//        }
//        $file = request()->file("file");
////        验证图片信息
//        if (!Validate::fileSize($file, 1 * 1024 * 1024)) {
//            return imageShow(1, "图片大小不能超过1MB");
//        }
//        if (!Validate::fileExt($file, 'jpeg,jpg,png,gif')) {
//            return imageShow(1, "图片类型错误");
//        }
////        tp6自定义上传文件
//        $filename = Filesystem::disk("public")->putFile("image", $file);
//        if (!$filename) {
//            return imageShow(1, "图片上传失败");
//        }
//        $imageUrl = "/upload/" . $filename;
//        return imageShow(0, "图片上传成功", $imageUrl);
//    }
}