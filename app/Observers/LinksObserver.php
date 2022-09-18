<?php

namespace App\Observers;

use App\Models\Links;

class LinksObserver
{
    /**
     * Handle the Links "created" event.
     *
     * @param  \App\Models\Links  $links
     * @return void
     */
    public function created(Links $links){
        //
    }

    /**
     * Handle the Links "updated" event.
     *
     * @param  \App\Models\Links  $links
     * @return void
     */
    public function updated(Links $links)
    {
        //
    }

    /**
     * Handle the Links "deleted" event.
     *
     * @param  \App\Models\Links  $links
     * @return void
     */
    public function deleted(Links $links) {
        $links->clicks()->delete();
    }

    /**
     * Handle the Links "restored" event.
     *
     * @param  \App\Models\Links  $links
     * @return void
     */
    public function restored(Links $links)
    {
        //
    }

    /**
     * Handle the Links "force deleted" event.
     *
     * @param  \App\Models\Links  $links
     * @return void
     */
    public function forceDeleted(Links $links)
    {
        //
    }
}
