<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ComposerPhoto extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'composer_photos';
    protected $primaryKey = 'id';

    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'file_name',
        'full_path',
        'composer_id',       
    ];
}
