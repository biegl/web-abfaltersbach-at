<?php

namespace App\Observers;

use App\Events\NewsCreated;
use App\Models\News;

class NewsObserver
{
    public function created(News $news): void
    {
        NewsCreated::dispatch($news);
    }

    public function deleted(News $news): void
    {
        $news->attachments()->delete();
    }
}
