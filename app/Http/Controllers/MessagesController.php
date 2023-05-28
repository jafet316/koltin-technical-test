<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessageEvent;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessagesController extends Controller
{
    /**
     * Get the list of messages of the given Chat
     * 
     * @param   \App\Models\Chat    $chat
     * @return  \Illuminate\Http\JsonResponse
     */
    public function index(Chat $chat): AnonymousResourceCollection {
        $messages = $chat->messages()
                    ->with(['user', 'chat.user', 'chat.post.user'])
                    ->orderBy('created_at', 'DESC')
                    ->paginate(15);

        return MessageResource::collection($messages);
    }

    /**
     * Create a new Message
     * 
     * @param   \App\Http\Requests\MessageRequest   $request
     * @return  mixed
     */
    public function store(MessageRequest $request, Chat $chat): JsonResponse {
        try {
            DB::beginTransaction();

            $message = Message::create([
                'chat_id'   => $chat->id,
                'user_id'   => Auth::id(),
                'message'   => $request->message
            ]);
            $message->load(['user', 'chat.user', 'chat.post.user']);

            NewChatMessageEvent::dispatch($chat, $message);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message'   => 'ERROR',
                'errors'    => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'message'   => 'OK'
        ]);
    }
}
