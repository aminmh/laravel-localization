<?php

namespace Bugloos\LaravelLocalization\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $table = 'translations';

    protected $fillable = ['text'];

    protected $hidden = ['created_at', 'updated_at'];

    public function label()
    {
        return $this->belongsTo(Label::class, 'label_id');
    }

    public function locale()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
