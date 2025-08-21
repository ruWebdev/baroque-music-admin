<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MusicalInstrument extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'musical_instruments';
    protected $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'title',
        'title_rod',
        'page_alias',
        'short_description',
        'long_description',
        'main_photo',
        'page_photo',
        'enabled',
    ];
}
