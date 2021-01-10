<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    use HasFactory;

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
