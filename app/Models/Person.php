<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Person extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'persons';

    protected static $logFillable = true;

    protected $fillable = [
        'name',
        'role',
        'phone',
        'email',
    ];

    public $with = ['image'];

    /**
     * Get person's image.
     */
    public function image()
    {
        return $this->morphOne(File::class, 'attachable');
    }
}
