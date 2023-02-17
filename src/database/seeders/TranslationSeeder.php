<?php

namespace Bugloos\LaravelLocalization\database\seeders;

use Bugloos\LaravelLocalization\Models\Label;
use Bugloos\LaravelLocalization\Models\Language;
use Bugloos\LaravelLocalization\Models\Translation;
use Bugloos\LaravelLocalization\Traits\ConfiguredTableName;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                        'text' => $this->{$locale}($label->key),
                        'label_id' => $label->id,
                        'language_id' => $localeId,
                    ]
                )->toArray()
            );
        }
    }

    private function nl(string $key)
    {
        return [
            'car' => 'auto',
            'home' => 'thuis',
            'job' => 'functie',
            'credit card' => 'kredietkaart',
            'bank' => 'bank',
            'mother' => 'moeder',
            'family' => 'familie',
            'plan' => 'plan',
            'map' => 'kaart',
            'world' => 'wereld-',
            'computer' => 'computer',
            'phone' => 'telefoon',
            'error' => 'fout',
            'label' => 'label',
            'doctor' => 'dokter',
            'teacher' => 'docent',
            'lesson' => 'les',
            'network' => 'netwerk',
            'internet' => 'internetten',
            'process' => 'werkwijze',
            'drag' => 'sleuren',
            'love' => 'liefde',
            'friend' => 'vriend',
            'realationship' => 'relatie',
            'dog' => 'hond',
            'engineer' => 'ingenieur',
        ][$key];
    }

    private function en(string $key)
    {
        $labels = [
            'car',
            'home',
            'job',
            'credit card',
            'bank',
            'mother',
            'family',
            'plan',
            'map',
            'world',
            'computer',
            'phone',
            'error',
            'label',
            'doctor',
            'teacher',
            'lesson',
            'network',
            'internet',
            'process',
            'drag',
            'love',
            'friend',
            'realationship',
            'dog',
            'engineer',
        ];

        return array_map(
            fn ($label) => ucwords($labels[$label]),
            array_flip($labels)
        )[$key];
    }
}
