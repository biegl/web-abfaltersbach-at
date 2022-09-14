<?php

namespace App\Listeners;

use Carbon\Carbon;

class SetEmailVerificationDate
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

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $user = $event->user;

        if (! (property_exists($user, 'email_verified_at') && $user->email_verified_at !== null)) {
            $user->email_verified_at = Carbon::now();
            $user->save();
        }
    }
}
