<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataCatalog extends Model
{
    use HasFactory;

    protected $table = 'data_catalog';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'name'
    ];

}
