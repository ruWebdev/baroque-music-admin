<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EventPhoto extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'event_photos';
    protected $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'file_name',
        'full_path',
        'event_id',
    ];
}
