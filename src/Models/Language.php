<?php

namespace Bugloos\LaravelLocalization\Models;

use Bugloos\LaravelLocalization\Traits\ConfiguredTableName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    use HasFactory;
    use ConfiguredTableName;

    public $timestamps = false;

    protected $table = 'languages';

    protected $fillable = ['locale', 'active', 'name'];

    protected $hidden = ['active'];

    protected $appends = ['flag'];

    protected function flag(): Attribute
    {
        return Attribute::get(function () {
            return ''; //TODO Return api endpoint to download flag file
        });
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
}
