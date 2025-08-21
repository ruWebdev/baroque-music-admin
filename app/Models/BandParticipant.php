<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

use App\Models\Band;

class BandParticipant extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'band_participants';
    protected $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'artist_id',
        'band_id',
        'role'
    ];

    public function band(): HasOne
    {
        return $this->HasOne(Band::class, "id", "band_id");
    }
}
