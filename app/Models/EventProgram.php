<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EventProgram extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'event_programs';
    protected $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'composer_id',
        'title',
        'sorting',
        'event_id',
    ];
}
