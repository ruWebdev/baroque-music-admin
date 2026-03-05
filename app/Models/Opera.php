<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Opera extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'operas';
    protected $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'page_alias',
        'title',
        'title_en',
        'year_created',
        'composer_id',
        'short_description',
        'long_description',
        'vk_video_link',
        'imslp_link',
        'enable_page',
        'main_photo',
        'page_photo',
    ];

    protected $casts = [
        'enable_page' => 'boolean',
    ];

    public function composer(): BelongsTo
    {
        return $this->belongsTo(Composer::class, 'composer_id', 'id');
    }
}
