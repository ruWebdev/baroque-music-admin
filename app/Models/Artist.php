<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'artists';
    protected $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'page_alias',
        'last_name',
        'first_name',
        'middle_name',
        'birth_place',
        'birth_date',
        'musical_instruments',
        'short_description',
        'long_description',
        'show_birth_place',
        'show_birth_date',
        'moderation_status',
        'enable_page',
        'main_photo',
        'page_photo',
        'video_type',
        'video_code',
    ];

    protected $casts = [
        'show_birth_place' => 'boolean',
        'show_birth_date' => 'boolean',
        'enable_page' => 'boolean',
    ];
}
