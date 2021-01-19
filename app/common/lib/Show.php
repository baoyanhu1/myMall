<?php


namespace app\common\lib;


class Show
{
    /**
     * 成功统一返回格式
     * @param array $data
     * @param string $message
     * @param int $httpStatus
     * @return \think\response\Json
     */
    public static function success($data = [],$message = "OK",$httpStatus = 200){
        $result = [
            "status" => config("status.success"),
            "message" => $message,
            "result" => $data,
        ];
        return json($result,$httpStatus);
    }

    /**
     * 失败统一返回格式
     * @param array $data
     * @param string $message
     * @param $status
     * @param int $httpStatus
     * @return \think\response\Json
     */
    public static function error($data = [],$message = "error",$status,$httpStatus = 200){
        $result = [
            "status" => $status,
            "message" => $message,
            "result" => $data,
        ];
        return json($result,$httpStatus);
    }
}