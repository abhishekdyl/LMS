<?php

namespace App\Listeners;

use App\Events\ProfileUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail; //---custom

// custom implements ShouldQueue
class SendProfileUpdateNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

     use InteractsWithQueue; //--custom
    /**
     * Handle the event.
     *
     * @param  \App\Events\ProfileUpdated  $event
     * @return void
     */
    public function handle(ProfileUpdated $event)
    {
        //---custom code
       $user = $event->user;

        // Send email notification
        Mail::send('email_profile_updated', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                    ->subject('Your Profile Has Been Updated');
        });
    }
}

