<?php

namespace Bugloos\LaravelLocalization\Models;

use Bugloos\LaravelLocalization\database\factories\CategoryFactory;
use Bugloos\LaravelLocalization\Traits\ConfiguredTableName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static Category create(array $attributes = [])
 */
class Category extends Model
{
    use HasFactory;
    use ConfiguredTableName;

    protected $table = 'categories';

    protected $fillable = ['name'];

    protected $hidden = ['created_at', 'updated_at'];

    public static function findBy(string|int $identifier): Model|Builder|null
    {
        $categoryQuery = static::query();

        if (is_numeric($identifier)) {
            return $categoryQuery->find($identifier);
        }

        return $categoryQuery->firstWhere('name', $identifier);
    }

    public function labels(): HasMany
    {
        return $this->hasMany(Label::class, 'category_id');
    }

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }
}
