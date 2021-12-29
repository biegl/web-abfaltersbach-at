<?php

namespace App\Models;

use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class News extends Model
{
    use HasFactory;
    use HasFilters;
    use LogsActivity;
    use Notifiable;

    protected $table = 'tbl_news';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    public $with = ['attachments'];

    public $appends = ['isExpired'];

    /**
     * @var string The name of the cache bucket.
     */
    public static $CACHE_KEY_TOP_NEWS = 'news.top';

    protected static $logFillable = true;

    protected $fillable = [
        'title',
        'text',
        'date',
        'expirationDate',
        'galleryId',
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
        'expirationDate' => 'datetime:Y-m-d',
    ];

    public function getIsExpiredAttribute()
    {
        if (is_null($this->expirationDate)) {
            return false;
        }

        return $this->expirationDate < date('Y-m-d');
    }

    /**
     * Scope a query to only include not expired news.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotExpired($query)
    {
        return $query
            ->whereDate('expirationDate', '>=', date('Y-m-d'))
            ->orWhereNull('expirationDate');
    }

    public function scopeTop($query)
    {
        return $query
            ->notExpired()
            ->orderBy('date', 'desc');
    }

    /**
     * Get all of the news' attachments.
     */
    public function attachments()
    {
        return $this->morphMany(File::class, 'attachable');
    }
}
