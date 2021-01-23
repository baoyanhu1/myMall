<?php


namespace app\common\lib;


use OSS\Core\OssException;
use OSS\OssClient;

class Oss
{
    /*
 * 图片上传，将图片上传至阿里云oss
 * */
    public function upload($file){
        // 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM账号进行API访问或日常运维，请登录RAM控制台创建RAM账号。
        $accessKeyId = config("aliyun.accessKeyId");
        $accessKeySecret = config("aliyun.accessKeySecret");
        $bucket = config("aliyun.bucketName");

        $files = $file;
        $name = $files['name'];
        $format = strrchr($name, '.');//截取文件后缀名如 (.jpg)

        // Endpoint以杭州为例，其它Region请按实际情况填写。
        $endpoint = "http://oss-cn-hangzhou.aliyuncs.com";

        // 设置文件名称。
        //这里是由sha1加密生成文件名 之后连接上文件后缀，生成文件规则根据自己喜好，也可以用md5
        $object = $bucket.'/'.sha1(date('YmdHis', time()) . uniqid()) . $format;
        // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt。
        $filePath = $files['tmp_name'];
        try{
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            $result = $ossClient->uploadFile($bucket, $object, $filePath);
            if(!$result){
                return ['status'=>0,'message'=>'上传失败'];
            }else{
                return ['status'=>1,'message'=>'上传成功','url'=>$result['info']['url']];
            }
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }

    /**
     * 删除单个OSS文件
     */
    public function delete($file){

// 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM账号进行API访问或日常运维，请登录 https://ram.console.aliyun.com 创建RAM账号。
        $accessKeyId = config("aliyun.accessKeyId");
        $accessKeySecret = config("aliyun.accessKeySecret");
// Endpoint以杭州为例，其它Region请按实际情况填写。
        $endpoint = "http://oss-cn-hangzhou.aliyuncs.com";
        $bucket = config("aliyun.bucketName");
        $object = $bucket.'/'.$file;
        try{
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

            $result= $ossClient->deleteObject($bucket, $object);
            if(!$result){
                return ['status'=>0,'message'=>'删除失败'];
            }else{
                return ['status'=>1,'message'=>'删除成功'];
            }
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }
}