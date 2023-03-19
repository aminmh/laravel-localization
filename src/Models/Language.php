<?php

namespace Bugloos\LaravelLocalization\Models;

use Bugloos\LaravelLocalization\Controller\LanguageController;
use Bugloos\LaravelLocalization\Database\Factories\LanguageFactory;
use Bugloos\LaravelLocalization\Traits\ConfiguredTableName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read  string $locale
 * @property-read  bool $active
 * @property-read  string $name
 */
class Language extends Model
{
    use HasFactory;
    use ConfiguredTableName;

    public const DEFAULT_FLAG_PATH = __DIR__ . '/../resources/assets/flags';

    public $timestamps = false;

    protected $table = 'languages';
    protected $fillable = ['locale', 'active', 'name'];

    protected $hidden = ['active'];

    protected $appends = ['flag'];

    public static function findOnly(string $locale, ?bool $active = null)
    {
        $query = static::query();

        if ($active) {
            $query = $query->scopes('actives');
        }

        if ($active === false) {
            $query = $query->scopes('inActives');
        }

        return $query->where('locale', $locale)->get();
    }

    protected static function newFactory(): LanguageFactory
    {
        return LanguageFactory::new();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class, 'language_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function avtivationToggle(bool $toggle)
    {
        return $this->update([
            'active' => $toggle,
        ]);
    }

    public function scopeInActives(Builder $query)
    {
        $query->where('active', 0);
    }

    public function scopeActives(Builder $query)
    {
        $query->where('active', 1);
    }

    protected function flag(): Attribute
    {
        return Attribute::get(function () {
            return action([LanguageController::class, 'flag'], ['locale' => $this->locale]);
        });
    }
}
