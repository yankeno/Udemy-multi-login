<?php

namespace App\Models;

use App\Models\SecondaryCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\PrimaryCategory
 *
 * @property int $id
 * @property string $name
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|SecondaryCategory[] $secondary
 * @property-read int|null $secondary_count
 * @method static \Illuminate\Database\Eloquent\Builder|PrimaryCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrimaryCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrimaryCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|PrimaryCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrimaryCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrimaryCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrimaryCategory whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrimaryCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PrimaryCategory extends Model
{
    use HasFactory;

    public function secondary(): HasMany
    {
        return $this->hasMany(SecondaryCategory::class);
    }
}
