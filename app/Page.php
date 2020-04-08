<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'tbl_site';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'ID',
        'seitentitel',
        'inhalt',
    ];
}
