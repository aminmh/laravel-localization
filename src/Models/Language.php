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
 * @method static LanguageFactory factory($count = null, $state = [])
 */
class Language extends Model
{
    use HasFactory;
    use ConfiguredTableName;

    public const DEFAULT_FLAG_PATH = __DIR__ . '/../resources/assets/flags';

    public $timestamps = false;

    protected $table = 'languages';
    protected $fillable = ['locale', 'name'];

    protected $attributes = ['active' => false];

    protected $hidden = ['active'];

    public $casts = ['active' => 'boolean'];

    protected $appends = ['flag'];

    public static function findActivatedByLocale(string $locale)
    {
        return static::query()->scopes('actives')->firstWhere('locale', $locale);
    }

    public static function findDeActivatedByLocale(string $locale)
    {
        return static::query()->scopes('inActives')->firstWhere('locale', $locale);
    }

    public static function findByLocale(string $locale): ?static
    {
        return static::query()->firstWhere('locale', $locale);
    }

    public function isActive(): bool
    {
        return $this->active === true;
    }

    public function isNotActive(): bool
    {
        return $this->active === false;
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

    public function activate(): void
    {
        $this->active = true;
        $this->save();
    }

    public function deActivate(): void
    {
        $this->active = false;
        $this->save();
    }

    public function scopeInActives(Builder $query): void
    {
        $query->where('active', 0);
    }

    public function scopeActives(Builder $query): void
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
