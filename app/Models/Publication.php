<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'publications';
    protected $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'page_alias',
        'title',
        'short_description',
        'long_description',
        'main_photo',
        'page_photo',
        'moderation_status',
        'enable_page',
        'artist_id',
        'page_views'
    ];
}
