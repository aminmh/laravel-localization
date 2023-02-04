<?php

namespace Bugloos\LaravelLocalization\Models;

use Bugloos\LaravelLocalization\database\factories\CategoryFactory;
use Bugloos\LaravelLocalization\Traits\ConfiguredTableName;
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

    public function labels(): HasMany
    {
        return $this->hasMany(Label::class, 'category_id');
    }

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }
}
