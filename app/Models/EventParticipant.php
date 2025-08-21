<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EventParticipant extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'event_participants';
    protected $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'artist_id',
        'band_id',
        'event_id',
        'type',
        'sorting',
    ];
}
