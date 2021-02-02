<?php
/**
 * 该文件主要存放业务代码
 */

return[
    "success" => 1,
    "error" => 0,
    "not_login" => -1,
    "user_is_register" => -2,
    "action_not_found" => -3,
    "controller_not_found" => -4,
//    mysql状态码
    "mysql" => [
        "table_normal" => 1,//正常
        "table_pending" => 0,//待审
        "table_delete" => -1,//删除

//        订单状态码支付状态 1：待支付 2：已支付 3：已发货 4：已收货 5：已完成 6：退款退货 7：已取消
        "pending_payment" => 1,//待支付
        "is_paid" => 2,//已支付
        "is_shipped" => 3,//已发货
        "is_received" => 4,//已收货
        "is_completed" => 5,//已完成
        "is_refund" => 6,//退款退货
        "is_cancelled" => 7,//已取消

//        是否为秒杀商品
        "is_spike" => 1,//是秒杀商品
        "is_no_spike" => 0,//不是秒杀商品
    ]
];