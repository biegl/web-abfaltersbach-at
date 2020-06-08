<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
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

    /**
     * Scope a query to only include upcoming events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        $start = Carbon::tomorrow();
        $end = Carbon::tomorrow()->addDays(30);

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
}
