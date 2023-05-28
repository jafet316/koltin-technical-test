<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
    /**
     * Create new chat
     * 
     * @param   \App\Models\Post    $post
     * @return  \Illuminate\Http\JsonResponse
     */
    public function store(Post $post): ChatResource {
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

        return new ChatResource($chat);
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
