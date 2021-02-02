<?php
/**
 * Created by PhpStorm.
 * User: jmsite.cn
 * Date: 2019/1/15
 * Time: 13:16
 */
namespace app\api\controller\rabbitmq;
use app\common\business\Order;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use think\facade\Log;

class Consumer
{
    /**
     * 消费端 消费端需要保持运行状态实现方式
     * 1 linux上写定时任务每隔5分钟运行下该脚本，保证访问服务器的ip比较平缓，不至于崩溃
     * 2 nohup php index.php index/Message_Consume/start &  用nohup命令后台运行该脚本
     * 3
     **/
    function shutdown($channel, $connection)
    {
        $channel->close();
        $connection->close();
        Log::write("closed",3);
    }

    function process_message($message)
    {
        echo  $message->body."\n";
//        下单逻辑处理
        $msg = $message->body;
        $orderBus = new Order();
        $orderBus->saveSpikeOrder($msg);
        //手动发送ack
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        // Send a message with the string "quit" to cancel the consumer.
        if ($message->body === 'quit') {
            $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
        }
    }

    /**
     * 启动
     * @return \think\Response
     */
    public function start()
    {
//        $param = config('rabbitmq.AMQP');
//        $amqpDetail = config('rabbitmq.order_queue');
        $params = [
            //    连接信息
            'AMQP' => [
                'host' => '47.110.139.172',
                'vhost' => '/',
                'port' => 5672,
                'login' => 'guest',
                'password' => 'guest',
            ],
            //  订单队列
            'order_queue' => [
                'exchange_name' => 'create_order_exchange',
                'exchange_type'=>'direct',#直连模式
                'queue_name' => 'create_order_queue',
                'route_key' => 'create_order_route',
                'consumer_tag' => 'consumer'
            ]
        ];
        $param = $params['AMQP'];

        $amqpDetail = $params['order_queue'];
        $connection = new AMQPStreamConnection(
            $param['host'],
            $param['port'],
            $param['login'],
            $param['password'],
            $param['vhost']
        );

        $channel = $connection->channel();
        $channel->queue_declare($amqpDetail['queue_name'], false, true, false, false);
        $channel->exchange_declare($amqpDetail['exchange_name'], 'direct', false, true, false);
        $channel->queue_bind($amqpDetail['queue_name'], 				   $amqpDetail['exchange_name'],$amqpDetail['route_key']);

        /*
            queue: 从哪里获取消息的队列
            consumer_tag: 消费者标识符
            no_local: 不接收此使用者发布的消息
            no_ack: 如果求设置为true，则此使用者将使用自动确认模式。详情请参见.
            exclusive:请独占使用者访问，这意味着只有这个使用者可以访问队列
            nowait:
            callback: :PHP回调 array($this, 'process_message') 调用本对象的process_message方法
        */

        $channel->basic_consume($amqpDetail['queue_name'], $amqpDetail['consumer_tag'], false, false, false, false, array($this, 'process_message'));
        register_shutdown_function(array($this, 'shutdown'), $channel, $connection);
        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }
}


