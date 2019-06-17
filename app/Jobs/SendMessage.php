<?php

namespace App\Jobs;

use App\Models\Customers;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Post data for group name and customer filter
     */
    public $post = [];

    /**
     * Create a new job instance.
     *
     * @param $data
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->post = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function handle()
    {
        // Create batch id for every new mailGroup
        $batchId = md5(microtime(true));
        $queueName = $this->post['mailGroup'] ?? 'default';
        $attempts = 1;

        $customers = Customers::whereRaw('1=1');

        // When there is customer_id send to queue only that customer
        if (isset($this->post['customer_id'])) {
            $customers->where('id', $this->post['customer_id']);
            $attempts = $this->post['attempts'];
            $batchId = $this->post['batch_id'];
            $queueName = $this->post['group_name'];
        } else {
            if (isset($this->post['sex'])) {
                $customers->where('sex', $this->post['sex']);
            }

            if (isset($this->post['country'])) {
                $customers->where('country_id', $this->post['country']);
            }
        }

        $result = $customers->get();

        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST'),
            env('RABBITMQ_PORT'),
            env('RABBITMQ_USER'),
            env('RABBITMQ_PASS'));

        $channel = $connection->channel();
        $channel->queue_declare($queueName, false, true, false, false);
        $channel->queue_declare('default', false, true, false, false);

        for ($i = 0; $i < count($result); $i++) {
            $data = [
                'id' => $result[$i],
                'batch_id' => $batchId,
                'group_name' => $queueName,
                'customer_id' => $result[$i]->id,
                'email' => $result[$i]->email,
                'attempts' => $attempts,
            ];

            $msg = new AMQPMessage(
                json_encode($data),
                array('delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT)
            );

            $channel->basic_publish($msg, '', $queueName);
        }
        $channel->close();
        $connection->close();
    }
}
