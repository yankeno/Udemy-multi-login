<?php

namespace App\Models;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'information',
        'filename',
        'is_sailing',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
