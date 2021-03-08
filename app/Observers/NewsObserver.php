<?php

namespace App\Observers;

use App\Models\News;
use App\Notifications\NewsCreated;

class NewsObserver
{
    /**
     * Handle the News "created" event.
     *
     * @param  \App\Models\News  $news
     * @return void
     */
    public function created(News $news)
    {
        $news->notify(new NewsCreated($news));
    }

    /**
     * Handle the News "deleted" event.
     *
     * @param  \App\Models\News  $news
     * @return void
     */
    public function deleted(News $news)
    {
        $news->attachments()->delete();
    }
}
