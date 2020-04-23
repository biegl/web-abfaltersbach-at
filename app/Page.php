<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\News;
use App\Event;

class Page extends Model
{
    protected $table = 'tbl_site';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'ID',
        'seitentitel',
        'inhalt',
    ];

    public function getContentAttribute()
    {
        return $this->inhalt;
    }

    public function getIsLandingPageAttribute()
    {
        return $this->template === 'template_home.php';
    }

    public function getTemplateNameAttribute()
    {
        if ($this->isLandingPage) {
            return 'page.home';
        }

        return 'page.default';
    }

    public function getModulesAttribute()
    {
        $content = $this->content;
        $navigation = Navigation::topLevel()->get();
        $breadcrumbs = Navigation::breadcrumbs($this);

        switch ($this->templateName) {
            case 'page.home':
                $news = News::top()->get();
                $grouped_events = Event::byMonth();
                return compact('content', 'navigation', 'breadcrumbs', 'news', 'grouped_events');
            default:
                return compact('content', 'navigation', 'breadcrumbs');
        }
    }
}
