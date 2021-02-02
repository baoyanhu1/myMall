<?php

return [
//    连接信息
    'AMQP' => [
        'host' => '47.110.139.172',
        'vhost' => '/',
        'port' => 5672,
        'login' => 'guest',
        'password' => 'guest',
    ],
//  创建订单队列
    'order_queue' => [
        'exchange_name' => 'create_order_exchange',
        'exchange_type'=>'direct',#直连模式
        'queue_name' => 'create_order_queue',
        'route_key' => 'create_order_route',
        'consumer_tag' => 'consumer'
    ]
];
