<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'tbl_news';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'text',
        'date',
        'expirationDate',
        'galleryId'
    ];

    protected $casts = [
        'date' => 'datetime',
        'expirationDate' => 'datetime',
    ];

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
            ->orderBy('date', 'desc')
            ->limit(20);
    }
}
