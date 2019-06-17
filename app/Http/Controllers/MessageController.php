<?php

namespace App\Http\Controllers;

use App\Http\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{

    /**
     * @param Request $request
     * @param MessageService $messageService
     *
     * @return JsonResponse
     * */
    public function send(Request $request, MessageService $messageService)
    {
        $messageService->send($request->post());

        return response()->json(['status' => 'success', 'message' => 'Messages added to queue']);
    }

    /**
     * @param MessageService $messageService
     * @return JsonResponse
     * */
    public function getMessageQueues(MessageService $messageService)
    {
        return response()->json(['status' => 'success', 'data' => $messageService->getActiveJobs()]);
    }
}
