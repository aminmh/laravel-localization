<?php

namespace Bugloos\LaravelLocalization\database\seeders;

use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Language;
use Bugloos\LaravelLocalization\Models\Translation;
use Bugloos\LaravelLocalization\Traits\ConfiguredTableName;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use function Pest\Faker\faker;

class TranslationSeeder extends Seeder
{
    use ConfiguredTableName;

    public function run()
    {
        $locales = Language::query()->whereIn('locale', ['nl', 'en'])->get()->pluck('id', 'locale');

        foreach ($locales as $locale => $localeId) {
            DB::table($this->getTable(Translation::class))->insert(
                Label::all()->map(
                    fn ($label) => [
                        'text' => faker()->sentence(),
                        'label_id' => $label->id,
                        'language_id' => $localeId,
                    ]
                )->toArray()
            );
        }
    }
}
