<?php

/**
 * Created by PhpStorm.
 * User: jmsite.cn
 * Date: 2019/1/15
 * Time: 13:15
 */

//$config = array(
//    'host' => '192.168.75.132',
//    'vhost' => '/',
//    'port' => 5672,
//    'login' => 'test',
//    'password' => 'test'
//);
$config = array(
    'host' => '47.110.139.172',
    'vhost' => '/',
    'port' => 5672,
    'login' => 'guest',
    'password' => 'guest'
);
//var_dump(1111);
$cnn = new AMQPConnection($config);
//var_dump(2222);
//var_dump($cnn->connect());
//die();
if (!$cnn->connect()) {
    echo "Cannot connect to the broker";
    exit();
}
$ch = new AMQPChannel($cnn);
$ex = new AMQPExchange($ch);
//消息的路由键，一定要和消费者端一致
$routingKey = 'key_1';
//交换机名称，一定要和消费者端一致，
$exchangeName = 'exchange_1';
$ex->setName($exchangeName);
$ex->setType(AMQP_EX_TYPE_DIRECT);
$ex->setFlags(AMQP_DURABLE);
$ex->declareExchange();
//创建10个消息
for ($i=1;$i<=55;$i++){
    //消息内容
    $msg = array(
        'data'  => 'message111_'.$i,
        'hello' => 'world111',
    );
    //发送消息到交换机，并返回发送结果
    //delivery_mode:2声明消息持久，持久的队列+持久的消息在RabbitMQ重启后才不会丢失
    echo "Send Message111:".$ex->publish(json_encode($msg), $routingKey, AMQP_NOPARAM, array('delivery_mode' => 2))."\n";
    //代码执行完毕后进程会自动退出
}
