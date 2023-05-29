<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
    /**
     * Get the list of chats of the current user
     * 
     * @return
     */
    public function getData(): AnonymousResourceCollection {
        $chats = Chat::where('user_id', Auth::id())
                    ->orWhereIn('post_id', function($q) {
                        $q->select('id')->from('posts')->where('user_id', Auth::id());
                    })
                    ->with([
                        'user', 
                        'post.user'
                    ])
                    //TODO: Order by las active chat
                    ->orderBy('created_at', 'DESC')
                    ->paginate(15);

        return ChatResource::collection($chats);
    }

    /**
     * Find an exist chat or create new to the given post
     * 
     * @param   \App\Models\Post    $post
     * @return  \Illuminate\Http\JsonResponse
     */
    public function findOrNew(Post $post): JsonResponse {
        // Check if already exist a chat for current user on this post
        $chat = Chat::where('post_id', $post->id)->where('user_id', Auth::id())->first();

        if(empty($chat)) {
            $chat = Chat::create([
                'post_id'   => $post->id,
                'user_id'   => Auth::id()
            ]);
        }

        // Load related data
        $chat->load(['post.user', 'user']);

        return response()->json([
            'message'   => 'OK',
            'data'      => new ChatResource($chat)
        ]);
    }

    /**
     * Get a chat
     * 
     * @param   \App\Models\Chat $chat
     * @return  \Illuminate\Http\JsonResponse
     */
    public function find(Chat $chat): JsonResponse {
        return response()->json([
            'data' => $chat
        ]);
    } 
}
