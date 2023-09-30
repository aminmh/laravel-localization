<?php

use Bugloos\LaravelLocalization\Facades\LocalizationFacade as Localization;
use Bugloos\LaravelLocalization\Models;
use Pest\Laravel as Assert;

//beforeEach(function () {
//    $this->locale = Models\Language::factory()->random()->createOne();
//    $this->locale->update(['active' => 1]);
//});

dataset('translate', [fn () => \Pest\Faker\faker()->sentence()]);
dataset('locale', [fn () => Models\Language::factory()->createOne()]);

it('make new category', function () {
    $category = Models\Category::factory()->createOne();

    \PHPUnit\Framework\assertNotNull($category);

    Assert\assertDatabaseHas('categories', [
        'name' => $category->getAttribute('name')
    ]);
});

it('make new label', function () {
    $category = Models\Category::factory()->createOne();

    $fakeLabel = Models\Label::factory()->withRealName()->makeOne();

    $label = Localization::addLabel($fakeLabel->getAttribute('key'), $category);

    \PHPUnit\Framework\assertNotNull($label);

    Assert\assertDatabaseHas('categories', [
        'name' => $category->getAttribute('name')
    ]);

    Assert\assertDatabaseHas('labels', [
        'key' => $label->getAttribute('key')
    ]);
});

it('activate a language', function () {
    $language = Models\Language::factory()->random()->create();
    expect($language)->toBeInstanceOf(Models\Language::class)->not->toBeNull();
    \Bugloos\LaravelLocalization\Facades\LocalizationFacade::activeLanguage($language->locale);
    $language = Models\Language::findActivatedByLocale($language->locale);
    expect($language)->not->toBeNull();
    \PHPUnit\Framework\assertTrue($language->active);
});

it('de-active a language', function (Models\Language $language) {
    expect($language)->toBeInstanceOf(Models\Language::class)->not->toBeNull();
    \PHPUnit\Framework\assertFalse($language->active);
    \Bugloos\LaravelLocalization\Facades\LocalizationFacade::activeLanguage($language->getAttribute('locale'));
    $language = Models\Language::findActivatedByLocale($language->locale);
    \PHPUnit\Framework\assertTrue($language->active);
    \Bugloos\LaravelLocalization\Facades\LocalizationFacade::deActiveLanguage($language->locale);
    $language = Models\Language::findDeActivatedByLocale($language->locale);
    \PHPUnit\Framework\assertNotNull($language);
    \PHPUnit\Framework\assertFalse($language->active);
})->with('locale');

it('translate a label', function (Models\Language $language) {
    $language->activate();
    $label = Models\Label::factory()->withRealName()->createOne();

    \PHPUnit\Framework\assertNotNull($label);

    \PHPUnit\Framework\assertInstanceOf(Models\Language::class, $language);

    $translate = \Pest\Faker\faker()->sentence();

    $translation = Localization::translate($label, $translate, $language->locale);

    \PHPUnit\Framework\assertNotNull($translation);

    Assert\assertDatabaseHas('translations', [
        'label_id' => $label->getKey(),
        'language_id' => $language->getKey()
    ]);
})->with('locale');

it('get translated labels', function (Models\Language $locale) {
    $locale->activate();
    expect($locale->active)->toBeTrue();
    $labels = Models\Label::factory()
        ->for(Models\Category::factory()->createOne())
        ->count(2)
        ->create();

    expect($labels)->toBeCollection();

    $translate = \Pest\Faker\faker()->sentence();

    Localization::translate($labels[0], $translate, $locale->locale);
    Localization::translate($labels[1], $translate, $locale->locale);

    $translatedLabels = Localization::translated($locale->locale)
        ->map(
            static function ($category) {
                return array_keys($category->translated_labels);
            }
        )->all()[0];

    \PHPUnit\Framework\assertContains($labels[0]->getAttribute('key'), $translatedLabels);
    \PHPUnit\Framework\assertContains($labels[1]->getAttribute('key'), $translatedLabels);
})->with('locale');

it('get un-translated labels in specific locale', function ($translate, Models\Language $locale) {
    $labels = Models\Label::factory()->count(2)->create();
$locale->activate();
    Localization::translate($labels[0], $translate, $locale->locale);

    $notTranslated = Localization::notTranslated($locale->locale);

    \PHPUnit\Framework\assertInstanceOf(Models\Language::class, $notTranslated);

    \PHPUnit\Framework\assertArrayHasKey('labels', $notTranslated->getAttributes());

    expect($notTranslated->getAttribute('labels'))->toBeCollection()->not->toBeEmpty();

    \PHPUnit\Framework\assertContains($labels[1]->id, $notTranslated->getAttribute('labels')->map(fn (Models\Label $label) => $label->getKey())->all());
})
    ->with('translate')
    ->with('locale');

it('get un-translated labels with specific category', function ($translate, Models\Language $locale) {
    $category = Models\Category::factory()->createOne();
$locale->activate();
    $labels = Models\Label::factory()->count(2)->withRealName()->for($category)->create();

    expect($labels)->toBeCollection()->not->toBeEmpty();

    Localization::translate($labels[0], $translate, $locale->locale);

    Assert\assertDatabaseHas('translations', [
        'label_id' => $labels[0]->getKey()
    ]);

    $notTranslated = Localization::notTranslated(category: $category);

    expect($notTranslated)->toBeCollection()->not->toBeEmpty();

    \PHPUnit\Framework\assertContains($labels[1]->id, $notTranslated->map(static fn (Models\Label $label) => $label->getKey()));

    \PHPUnit\Framework\assertEquals($labels[1]->category, $notTranslated->first()->category);
})
    ->with('translate')
    ->with('locale');

it('translates a label into multiple languages', function () {
    $languages = Models\Language::factory()->count(2)->random()->create();

    /** @var Models\Label $label */
    $label = Models\Label::factory()->withRealName()->createOne();

    expect($languages)->toBeCollection()->not->toBeEmpty();

    $languages = $languages->each(static fn (Models\Language $language) => $language->activate())->sortBy('id');

    $translations = $languages->flatMap(static fn (Models\Language $language) => [$language->locale => fake()->sentence()])->all();

    $result = Localization::bulkTranslate($label, $translations);

    \PHPUnit\Framework\assertTrue($result);

    $translated = $label->translations()->whereIn('language_id', $languages->pluck('id')->all())->get()->sortBy('id');

    expect($translated)->toBeCollection()->not->toBeEmpty();

    \PHPUnit\Framework\assertEquals($translated[0]->locale['id'], $languages[0]->getKey());

    \PHPUnit\Framework\assertEquals($translated[1]->locale['id'], $languages[1]->getKey());
});

it('translate label locally', function () {
    $language = Models\Language::factory()->createOne();
    $language->activate();
    Artisan::call('localization:migrate', ['path' => lang_path(),'--lang' => 'en']);
    $translated = translate('auth.failed', [], 'en');
    \PHPUnit\Framework\assertNotSame('auth.failed', $translated);
});
