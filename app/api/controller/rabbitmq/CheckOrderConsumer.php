<?php


namespace app\api\controller\rabbitmq;


use app\common\business\Order;
use think\Exception;

class CheckOrderConsumer
{
    public function start(){
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
            //$exchange->setFlags(AMQP_DURABLE);//声明一个已存在的交换器的，如果不存在将抛出异常，这个一般用在consume端
            $exchange->setName($params['exchangeName']);
            $exchange->setType('x-delayed-message'); //x-delayed-message类型
            /*RabbitMQ常用的Exchange Type有三种：fanout、direct、topic。

              fanout:把所有发送到该Exchange的消息投递到所有与它绑定的队列中。

              direct:把消息投递到那些binding key与routing key完全匹配的队列中。

              topic:将消息路由到binding key与routing key模式匹配的队列中。*/
            $exchange->setArgument('x-delayed-type','direct');
            $exchange->declareExchange();

            //$channel->startTransaction();

            $queue = new \AMQPQueue($channel);
            $queue->setName($params['queueName']);
            $queue->setFlags(AMQP_DURABLE);
            $queue->declareQueue();

            //绑定
            $queue->bind($params['exchangeName'], $params['routeKey']);
        } catch(Exception $e) {
            //TODO 记录日志
            echo $e->getMessage();
            exit();
        }

        function callback(AMQPEnvelope $message) {
            global $queue;
            if ($message) {
                $body = $message->getBody();
                echo '接收时间：'.date("Y-m-d H:i:s", time()). PHP_EOL;
                echo '接收内容：'.$body . PHP_EOL;
                //为了防止接收端在处理消息时down掉，只有在消息处理完成后才发送ack消息
                $queue->ack($message->getDeliveryTag());
            } else {
                echo 'no message' . PHP_EOL;
            }
        }

//$queue->consume('callback');  第一种消费方式,但是会阻塞,程序一直会卡在此处

//第二种消费方式,非阻塞
        /*$start = time();
        while(true)
        {
            $message = $queue->get();
            if(!empty($message))
            {
                echo $message->getBody();
                $queue->ack($message->getDeliveryTag());    //应答，代表该消息已经消费
                $end = time();
                echo '<br>' . ($end - $start);
                exit();
            }
            else
            {
                //echo 'message not found' . PHP_EOL;
            }
        }*/

//注意：这里需要注意的是这个方法：$queue->consume，queue对象有两个方法可用于取消息：consume和get。前者是阻塞的，无消息时会被挂起，适合循环中使用；后者则是非阻塞的，取消息时有则取，无则返回false。
//就是说用了consume之后，会同步阻塞，该程序常驻内存，不能用nginx，apache调用。
        $action = '2';

        if($action == '1'){
            $queue->consume('callback');  //第一种消费方式,但是会阻塞,程序一直会卡在此处
        }else{
            //第二种消费方式,非阻塞
            $start = time();
            while(true)
            {
                $message = $queue->get();
                if(!empty($message))
                {
                    echo '接收时间：'.date("Y-m-d H:i:s", time()). PHP_EOL;
                    echo '接收内容：'.$message->getBody().PHP_EOL;
                    $msg = $message->getBody();
                    $orderBus = new Order();
                    $orderBus->checkOrderStatus($msg);
                    $queue->ack($message->getDeliveryTag());    //应答，代表该消息已经消费
                    $end = time();
                    echo '运行时间：'.($end - $start).'秒'.PHP_EOL;
                    //exit();
                }
                else
                {
                    //echo 'message not found' . PHP_EOL;
                }
            }
        }
    }
}