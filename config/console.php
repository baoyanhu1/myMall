<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        // consumer是app\Command\Consumer文件中自定义命令行的名字
        //命令行启动创建订单的消费者
        'consumer' => 'app\Command\Consumer',
        //命令行启动查询订单是否支付消费者
        'check_order_consumer' => 'app\Command\CheckOrderConsumer',
    ],
];
