<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
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
