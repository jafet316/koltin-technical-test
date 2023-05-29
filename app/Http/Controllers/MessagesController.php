<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessageEvent;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessagesController extends Controller
{
    /**
     * Get the list of messages of the given Chat
     * 
     * @param   \Illuminate\Http\Request    $request
     * @param   \App\Models\Chat    $chat
     * @return  \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, Chat $chat): AnonymousResourceCollection {
        $limit = 15;

        if($request->has('single')) {
            $limit = 1;
        }

        $messages = $chat->messages()
                    ->with(['user'])
                    ->orderBy('created_at', 'DESC')
                    ->paginate($limit);

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

            if($request->hasFile('attachment')) {
                $fileInfo = $this->storeAttachment($message, $request->attachment);

                $message->attachment            = $fileInfo['path'];
                $message->attachment_name       = $fileInfo['name'];
                $message->attachment_size       = $fileInfo['size'];
                $message->attachment_extension  = $fileInfo['extension'];
                $message->save();
            }

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
            'message'   => 'OK',
            'data'      => new MessageResource($message)
        ]);
    }

    /**
     * Download the attached file on a message
     * TODO: Add middleware to prevent unautorized downloads
     * 
     * @param   \App\Models\Message $message
     * @return  \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadAttachment(Message $message): StreamedResponse {
        //Abort if file not exist
        if (!Storage::exists($message->attachment)) {
            abort(404); 
        }

        return Storage::download($message->attachment, $message->attachment_name);

    }

    /**
     * Store the mesage attachment and return the file info
     * 
     * @param   \App\Models\Message $message
     * @param   \Illuminate\Http\UploadedFile   $file 
     * @return  array
     */
    private function storeAttachment(Message $message, UploadedFile $file): array {
        try {
            $fileName       = $file->getClientOriginalName();
            $fileSize       = $file->getSize();
            $fileExtension  = $file->extension();

            $filePath = Auth::id() . 
                        '/posts/' . 
                        $message->chat->post->id . 
                        '/chats/' .
                        $message->chat->id .
                        '/attachments/' .
                        date('YmdHis') .
                        '.' . 
                        $file->extension(); 
            
            Storage::disk('local')->put($filePath, file_get_contents($file));
        } catch (\Throwable $th) {
            throw $th;
        }

        return [
            'path'      => $filePath,
            'name'      => $fileName,
            'size'      => $fileSize,
            'extension' => $fileExtension
        ];
    }
}
