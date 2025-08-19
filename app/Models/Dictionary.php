<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Dictionary extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'dictionary';
    protected $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'page_alias',
        'title',
        'origin_language',
        'transcription',
        'short_description',
        'long_description',
        'main_photo',
        'page_photo',
        'moderation_status',
        'enable_page',
        'external_link',
        'user_id',
        'page_views'
    ];

    protected $casts = [
        'enable_page' => 'boolean',
    ];
}
