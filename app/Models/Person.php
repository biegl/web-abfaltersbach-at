<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

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
