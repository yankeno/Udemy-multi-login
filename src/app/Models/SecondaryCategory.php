<?php

namespace App\Models;

use App\Models\PrimaryCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecondaryCategory extends Model
{
    use HasFactory;

    public function primary(): BelongsTo
    {
        return $this->belongsTo(PrimaryCategory::class);
    }
}
