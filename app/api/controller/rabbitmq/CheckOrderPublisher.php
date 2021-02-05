<?php


namespace app\api\controller\rabbitmq;


class CheckOrderPublisher
{
    public static function pushMessage($message)
    {
        $params = array(
            'exchangeName' => 'check_order_exchange',
            'queueName' => 'check_order_queue',
            'routeKey' => 'check_order_route',
        );
        $connectConfig = array(
            'host' => '47.110.139.172',
            'port' => 5672,
            'login' => 'guest',
            'password' => 'guest',
            'vhost' => '/'
        );

        try {
            $conn = new \AMQPConnection($connectConfig);
            $conn->connect();
            if (!$conn->isConnected()) {
                //die('Conexiune esuata');
                //TODO 记录日志
                echo 'rabbit-mq 连接错误:', json_encode($connectConfig);
                exit();
            }
            $channel = new \AMQPChannel($conn);
            if (!$channel->isConnected()) {
                // die('Connection through channel failed');
                //TODO 记录日志
                echo 'rabbit-mq Connection through channel failed:', json_encode($connectConfig);
                exit();
            }
            $exchange = new \AMQPExchange($channel);
            $exchange->setName($params['exchangeName']);
            $exchange->setType('x-delayed-message'); //x-delayed-message类型
            /*RabbitMQ常用的Exchange Type有三种：fanout、direct、topic。

              fanout:把所有发送到该Exchange的消息投递到所有与它绑定的队列中。

              direct:把消息投递到那些binding key与routing key完全匹配的队列中。

              topic:将消息路由到binding key与routing key模式匹配的队列中。*/
            $exchange->setArgument('x-delayed-type','direct');
            $exchange->declareExchange();

            //$channel->startTransaction();
            //RabbitMQ不容许声明2个相同名称、配置不同的Queue,否则报错
            $queue = new \AMQPQueue($channel);
            $queue->setName($params['queueName']);
            $queue->setFlags(AMQP_DURABLE);
            $queue->declareQueue();

            //绑定队列和交换机
            $queue->bind($params['exchangeName'], $params['routeKey']);
            //$channel->commitTransaction();
        } catch(Exception $e) {
            //TODO 记录日志
        }
        //延迟时间'x-delay'=> 60000参数毫秒  60000为一分钟
        $exchange->publish($message, $params['routeKey'], AMQP_NOPARAM, ['headers'=>['x-delay'=> 60000 * 3]]);
        echo '当前发送消息时间：'.date("Y-m-d H:i:s", time());
//        for($i=5;$i>0;$i--){
//            //生成消息
//            echo '发送时间：'.date("Y-m-d H:i:s", time()).PHP_EOL;
//            echo 'i='.$i.'，延迟'.$i.'秒'.PHP_EOL;
//            $message = json_encode(['order_id'=>time(),'i'=>$i]);
//            $exchange->publish($message, $params['routeKey'], AMQP_NOPARAM, ['headers'=>['x-delay'=> 1000*$i]]);
//            sleep(2);
//        }
        $conn->disconnect();

    }
}