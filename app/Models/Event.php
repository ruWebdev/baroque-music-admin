<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'events';
    protected $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'page_alias',
        'event_type',
        'user_id',
        'event_date',
        'event_time',
        'title',
        'place',
        'additional_artists',
        'contents',
        'age_restrictions',
        'ticket_price_from',
        'ticket_price_to',
        'external_link',
        'can_preorder',
        'sold_out',
        'enable_page',
        'moderation_status',
        'archived',
        'featured',
        'main_photo',
        'page_photo',
        'tags',
        'views_count',
    ];

    protected $casts = [
        'enable_page' => 'boolean',
        'sold_out' => 'boolean',
        'featured' => 'boolean'
    ];

    public function program(): HasMany
    {
        return $this->HasMany(EventProgram::class, "event_id", "id");
    }

    public function participants(): HasMany
    {
        return $this->HasMany(EventParticipant::class, "event_id", "id");
    }
}
