<?php

namespace App\Console\Commands;

use App\Http\Services\MessageService;
use App\Jobs\SendMessage;
use App\Models\MessageLog;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitWorker extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbit-worker {queueName?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rabbit Worker';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function handle()
    {
        $queueName = $this->argument('queueName');

        // Connect to rabbitmq server
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST'),
            env('RABBITMQ_PORT'),
            env("RABBITMQ_USER"),
            env("RABBITMQ_PASS")
        );
        $channel = $connection->channel();

        // Get active queues
        $ms = new MessageService();
        $activeQueues = $ms->getActiveJobs();

        $queueFound = false;

        if ($queueName) {
            foreach ($activeQueues as $queue) {
                if ($queue->name === $queueName) {
                    $queueFound = true;
                    break;
                }
            }
        }

        if ($queueName && !$queueFound) {
            echo 'Queue with name: "' . $queueName . '" not found' . PHP_EOL;
            return false;
        }

        // Declare Queue
        if ($queueName && $queueFound) {
            $channel->queue_declare($queueName, false, true, false, false);
        } else {
            foreach ($activeQueues as $queue) {
                if (isset($queue->messages) && $queue->messages > 0) {
                    $channel->queue_declare($queue->name, false, true, false, false);
                }
            }
        }

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
            $message = json_decode($msg->body);

            sleep(2);
            $status = 'success';

            // Message Sent issue imitations
            if ($message->customer_id % 5 == 0 && $message->attempts < 2) {
                $status = 're_queued';
            }

            // Log Message processing result
            MessageLog::create([
                'group_name' => $message->group_name,
                'batch_id' => $message->batch_id,
                'customer_id' => $message->customer_id,
                'attempts' => $message->attempts,
                'status' => $status,
            ]);

            // On message processing issues resend it to queue
            if ($status == 're_queued') {
                $this->dispatch(new SendMessage([
                    'group_name' => $message->group_name,
                    'batch_id' => $message->batch_id,
                    'customer_id' => $message->customer_id,
                    'attempts' => $message->attempts + 1
                ]));
            }
            echo " [x] Done\n";
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_qos(null, 1, null);

        // Create Consumer
        if ($queueName && $queueFound) {
            $channel->basic_consume($queueName, '', false, false, false, false, $callback);
        } else {
            foreach ($activeQueues as $queue) {
                if (isset($queue->messages) && $queue->messages > 0) {
                    $channel->basic_consume($queue->name, '', false, false, false, false, $callback);
                }
            }
        }

        while (count($channel->callbacks)) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();

        return true;
    }
}
