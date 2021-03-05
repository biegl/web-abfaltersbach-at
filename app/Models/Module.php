<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;

class Module extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'modules';

    protected static $logFillable = true;

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
