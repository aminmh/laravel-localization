<?php

namespace Bugloos\LaravelLocalization\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Localization\Database\factories\LabelFactory;

class Label extends Model
{
    use HasFactory;

    protected $table = 'labels';

    protected $fillable = ['key'];

    protected $hidden = ['created_at', 'updated_at'];

    protected static function newFactory()
    {
        // return LabelFactory::new();
    }

    public function translations()
    {
        return $this->hasMany(Translation::class, 'label_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
