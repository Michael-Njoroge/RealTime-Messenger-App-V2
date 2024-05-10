<?php

namespace App\Jobs;

use App\Events\GroupDeleted;
use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteGroupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $id = $this->group->id;
        $name = $this->group->name;
        $this->group->last_message_id = null;
        $this->group->save();
        
        //Iterate over all messages and delete them
        $this->group->messages->each->delete();
        
        //Remove all users from the group
        $this->group->users()->detach();
        
        //Delete the actual group now
        $this->group->delete();

        //Group delete event
        GroupDeleted::dispatch($id,$name);
    }
}