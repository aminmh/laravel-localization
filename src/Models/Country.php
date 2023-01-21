<?php

namespace Bugloos\LaravelLocalization\Models;

use Bugloos\LaravelLocalization\Traits\ConfiguredTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    use ConfiguredTableName;

    protected $table = 'countries';

    protected $fillable = ['code','name'];

    public function languages()
    {
        return $this->hasMany(Language::class, 'country_id');
    }
}
