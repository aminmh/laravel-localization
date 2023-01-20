<?php

namespace Bugloos\LaravelLocalization\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $table = 'languages';

    protected $fillable = ['country', 'locale', 'active', 'country_name', 'locale_name'];

    protected $hidden = ['created_at', 'updated_at', 'active', 'flag'];

    protected $appends = ['flag', 'default'];

    protected function flag(): Attribute
    {
        return Attribute::get(function () {
            $config = config('localization.flags');

            return "{$config['path']}".$this->attributes[$config['name_map_to']].".{$config['format']}";
        });
    }

    public function translations()
    {
        return $this->hasMany(Translation::class, 'language_id');
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
