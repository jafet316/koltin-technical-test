<?php

namespace App\Console\Commands;

use App\Mail\SendDailyChatsMail;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyChatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-chats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the daily chats to all register users';

    /**
     * Execute the console command.
     */
    public function handle()
    {  
        $users = User::all();

        // TODO: Optimize query to avoid ejectute in loop
        foreach ($users as $user) {
            $chats = Chat::query()
                        ->where(function($q) use($user) {
                            $q->where('user_id', $user->id);
                            $q->orWhereIn('post_id', function($q2) use($user) {
                                $q2->select('id')->from('posts')->where('user_id', $user->id);
                            });
                        })
                        ->whereIn('id', function($q) {
                            $q->select('chat_id')->from('messages')->whereDate('created_at', date('Y-m-d'));
                        })
                        ->with([
                            'post',
                            'messages' => function($q) {
                                $q->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'ASC');
                            },
                            'messages.user'
                        ])
                        ->get();

            $zip = new \ZipArchive;
            $tmpDir  = "app/tmp/{$user->id}/chats"; 
            $zipName = "app/tmp/{$user->id}/chats_". date('Y_m_d') .'.zip';

            if (!\File::exists(storage_path($tmpDir))) {
                \File::makeDirectory(storage_path($tmpDir), 0755, true);
            }
            
            if ($zip->open(storage_path($zipName), \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
                foreach ($chats as $chat) {
                    $zip->addEmptyDir($chat->post->title);
                    $zip->addEmptyDir($chat->post->title . '/attachments');
    
                    $txt = "[post]: {$chat->post->title}\n" ;
                    
                    foreach ($chat->messages as $message) {
                        $txt    .= "[{$message->created_at->format('Y-m-d H:i:s')}][{$message->user->name}]: {$message->message}" .($message->attachment ? '(Archivo adjunto)' : '') . "\n"; 
    
                        if($message->attachment) {
                            $attachmentFileName =  "{$chat->post->title}/attachments/{$message->attachment_name}.{$message->attachment_extension}";
                            $zip->addFile(storage_path("app/$message->attachment"), $attachmentFileName);
                        }
                    }
    
                    $zip->addFromString($chat->post->title . '/chat.txt', $txt);
                }
                   
                $zip->close();
            }
    
            Mail::to($user->email)->queue(new SendDailyChatsMail($zipName));
        }
    }
}
