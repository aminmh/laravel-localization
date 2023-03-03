<?php

namespace Bugloos\LaravelLocalization\Models;

use Bugloos\LaravelLocalization\database\factories\LabelFactory;
use Bugloos\LaravelLocalization\Traits\ConfiguredTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read string $key
 * @property-read Category $category
 */
class Label extends Model
{
    use HasFactory;
    use ConfiguredTableName;

    protected $table = 'labels';

    protected $fillable = ['key'];

    protected $hidden = ['created_at', 'updated_at'];

    public static function findBy(string|int $identifier, string $category)
    {
        $labelQuery = static::query();

        if (is_numeric($identifier)) {
            return $labelQuery->find($identifier);
        }

        return $labelQuery
            ->whereRelation('category', 'name', $category)
            ->firstWhere('key', $identifier);
    }

    protected static function newFactory()
    {
        return LabelFactory::new();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class, 'label_id');
    }

    public function notTranslated(): HasMany
    {
        return $this->hasMany(Translation::class, 'label_id')
            ->where('text', null);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(Translation::class, 'label_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
