<?php


namespace app\api\controller\rabbitmq;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Publisher
{

    public static function pushMessage($data)
    {
        $param = config('rabbitmq.AMQP');
        $amqpDetail = config('rabbitmq.order_queue');
        $connection = new AMQPStreamConnection(
            $param['host'],
            $param['port'],
            $param['login'],
            $param['password'],
            $param['vhost']
        );
        $channel = $connection->channel();
        /*
             name: $queue  创建队列
             passive: false
             持久durable: true // //队列将在服务器重启后继续存在
             互斥exclusive: false // 队列可以通过其他渠道访问
             auto_delete: false 通道关闭后，队列不会被删除
         */
        $channel->queue_declare($amqpDetail['queue_name'], false, true, false, false);

        /*
            name: $exchange  创建交换机
            type: direct   直连方式
            passive: false
            durable: true  持久// 交换器将在服务器重启后继续存在
            auto_delete: false //一旦通道关闭，交换器将不会被删除。
        */
        $channel->exchange_declare($amqpDetail['exchange_name'], 'direct', false, true, false);

        /*
             $messageBody:消息体
             content_type:消息的类型 可以不指定
             delivery_mode:消息持久化最关键的参数
             AMQPMessage::DELIVERY_MODE_NON_PERSISTENT = 1;
             AMQPMessage::DELIVERY_MODE_PERSISTENT = 2;
         */
        $messageBody = $data;
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, $amqpDetail['exchange_name'],$amqpDetail['route_key']);
        $channel->close();
        $connection->close();
//        echo  "ok";
    }
}