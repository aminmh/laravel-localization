<?php

namespace Bugloos\LaravelLocalization\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = ['name'];

    protected $hidden = ['created_at', 'updated_at'];

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function labels()
    {
        return $this->hasMany(Label::class, 'category_id');
    }
}
