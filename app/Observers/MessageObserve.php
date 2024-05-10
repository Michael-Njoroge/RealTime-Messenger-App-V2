<?php

namespace App\Observers;

use App\Models\Group;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Support\Facades\Storage;

class MessageObserve
{
    public function deleting(Message $message)
    {
        //Iterate over the message's attachments and delete them from the file system
        $message->attachment->each(function ($attachment) {
            //Delete attachment file from file system saved on public disk
            $dir = dirname($attachment->path);
            Storage::disk('public')->deleteDirectory($dir);
        });

        //delete all attachments related to the message from the database
        $message->attachment()->delete();

        if($message->group_id){
            $group = Group::where('last_message_id', $message->id)->first();

            if($group){
                $prevMessage = Message::where('group_id',$message->group_id)
                    ->where('id', '!=', $message->id)
                    ->latest()
                    ->limit(1)
                    ->first();

                    if($prevMessage){
                        $group->last_message_id = $prevMessage->id;
                        $group->save();
                    }
            }
        }
        else{
            $conversation = Conversation::where('last_message_id', $message->id)->first();

            //if the conversation is the last message in the conversation
            if($conversation){
                $prevMessage = Message::where(function ($query) use ($message) {
                    $query->where('sender_id', $message->sender_id)->where('receiver_id', $message->receiver_id)->orWhere('sender_id', $message->receiver_id)->where('receiver_id', $message->sender_id);})->where('id', '!=', $message->id)->latest()->limit(1)->first();

                    if($prevMessage){
                        $conversation->last_message_id = $prevMessage->id;
                        $conversation->save();
                    }
            }
        }
    }
}
