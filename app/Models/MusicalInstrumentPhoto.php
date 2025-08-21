<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MusicalInstrumentPhoto extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'musical_instrument_photos';
    protected $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'file_name',
        'full_path',
        'musical_instrument_id',
    ];
}
