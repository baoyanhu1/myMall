<?php
// 应用公共文件
use phpass\PasswordHash;


/**
 * @param $status
 * @param string $message
 * @param array $data
 * @param int $httpStatus
 * @return \think\response\Json
 * 通用api格式输出
 */
function show($status,$message = "error",$data = [],$httpStatus = 200){
    $result = [
        "status" => $status,
        "message" => $message,
        "result" => $data,
    ];
    return json($result,$httpStatus);
}

/**
 * 生成phpass加密
 */
function phpass($password)
{
    $t_hasher = new PasswordHash(8, FALSE);
    $correct = $password;
    $hash = $t_hasher->HashPassword($correct);
    return $hash;
}

/**
 * 验证phpass加密密码是否正确
 */
function checkPhpass($password,$old_password){
    $t_hasher = new PasswordHash(8, FALSE);
    $check = $t_hasher->CheckPassword($password, $old_password);
    return $check;
}

/**
 * 返回layui富文本编辑器需要的json格式数据
 * @param int $code
 * @param string $message
 * @param string $src
 * @param string $title
 * @param int $httpStatus
 * @return \think\response\Json
 */
function imageShow($code = 0,$message = "",$src = "",$title = "",$httpStatus = 200){
    $result = [
        'code' => $code,
        'msg' => $message,
        'data' => [
            'src' => $src,
            'title' => $title
        ]
    ];
    return json($result,$httpStatus);
}








