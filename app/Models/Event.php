<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $table = 'tbl_events';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'date',
        'text',
        'filepath',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public $with = ['attachments'];

    /**
     * @var string The name of the cache bucket.
     */
    public static $CACHE_KEY_GROUPED_EVENTS = 'events.grouped_by_month';

    /**
     * @var string The name of the cache bucket.
     */
    public static $CACHE_KEY_CURRENT_EVENTS = 'events.current';

    /**
     * Scope a query to only include upcoming events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        $start = Carbon::tomorrow();
        $end = Carbon::tomorrow()->addMonths(3);

        return $query
            ->whereBetween('date', [$start, $end])
            ->orderBy('date', 'asc');
    }

    /**
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrent($query)
    {
        return $query
            ->where('date', Carbon::today())
            ->orderBy('date', 'asc');
    }

    /**
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMonth($query)
    {
        return $query->upcoming()->get()->groupBy(function ($d) {
            return Carbon::parse($d->date)->formatLocalized('%B');
        });
    }

    /**
     * Get all of the event's attachments.
     */
    public function attachments()
    {
        return $this->morphMany(File::class, 'attachable');
    }
}
