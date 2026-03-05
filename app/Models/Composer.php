<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Composer extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'composers';
    protected $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'page_alias',
        'last_name',
        'last_name_en',
        'last_name_rod',
        'first_name',
        'first_name_en',
        'first_name_rod',
        'first_name_short',
        'first_name_short_en',
        'birth_date',
        'death_date',
        'main_photo',
        'page_photo',
        'short_description',
        'long_description',
        'imslp_link',
        'video_type',
        'video_code',
    ];
}
