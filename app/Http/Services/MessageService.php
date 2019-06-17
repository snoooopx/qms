<?php

namespace App\Http\Services;

use App\Http\Controllers\Controller;
use App\Jobs\SendMessage;

class MessageService extends Controller
{
    /**
     * @param array $data
     * */
    public function send(Array $data)
    {
        $this->dispatch(new SendMessage($data));
    }

    /**
     * Get RabbitMQ Active queues from API
     * */
    public function getActiveJobs()
    {
        $url = env('RABBITMQ_HOST') . ':' . env('RABBITMQ_HTTP_PORT') . '/api/queues';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERPWD, env("RABBITMQ_USER") . ":" . env("RABBITMQ_PASS"));
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url
        ));

        $response = curl_exec($curl);

        if ($response) {
            $response = json_decode($response);
        }

        curl_close($curl);

        return $response;
    }
}
