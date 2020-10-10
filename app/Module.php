<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{

    protected $table = 'modules';

    protected $fillable = [
        'type',
        'configuration',
    ];

    protected $casts = [
        'configuration' => 'array',
    ];

    /**
     * Get all of the owning insertable models.
     */
    public function insertable()
    {
        return $this->morphTo();
    }
}
