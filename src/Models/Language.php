<?php

namespace Bugloos\LaravelLocalization\Models;

use Bugloos\LaravelLocalization\Traits\ConfiguredTableName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;
    use ConfiguredTableName;

    public $timestamps = false;

    protected $table = 'languages';

    protected $fillable = ['locale', 'active', 'name'];

    protected $hidden = ['active'];

    protected $appends = ['flag', 'default'];

    public function translations()
    {
        return $this->hasMany(Translation::class, 'language_id');
    }

    public function country()
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
            $config = config('localization.flags');

            return "{$config['path']}".$this->attributes[$config['name_map_to']].".{$config['format']}";
        });
    }
}
