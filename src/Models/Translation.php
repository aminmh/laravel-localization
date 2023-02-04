<?php

namespace Bugloos\LaravelLocalization\Models;

use Bugloos\LaravelLocalization\Traits\ConfiguredTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Translation extends Model
{
    use HasFactory;
    use ConfiguredTableName;

    protected $table = 'translations';

    protected $fillable = ['text'];

    protected $hidden = ['created_at', 'updated_at'];

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class, 'label_id');
    }

    public function locale(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
