<?php

use Bugloos\LaravelLocalization\Facades\LocalizationFacade as Localization;
use Bugloos\LaravelLocalization\Models;
use Pest\Laravel as Assert;

beforeEach(function () {
    $this->locale = Models\Language::factory()->createOne();
    $this->locale->update(['active' => 1]);
});

dataset('translate', [fn () => \Pest\Faker\faker()->sentence()]);
dataset('locale', [fn () => 'en']);

it('make new category', function () {
    $category = Models\Category::factory()->createOne();

    \PHPUnit\Framework\assertNotNull($category);

    Assert\assertDatabaseHas('categories', [
        'name' => $category->getAttribute('name')
    ]);
});

it('make new label', function () {
    $category = Models\Category::factory()->createOne();

    $fakeLabel = Models\Label::factory()->makeOne();

    $label = Localization::addLabel($fakeLabel->getAttribute('key'), $category);

    \PHPUnit\Framework\assertNotNull($label);

    Assert\assertDatabaseHas('categories', [
        'name' => $category->getAttribute('name')
    ]);

    Assert\assertDatabaseHas('labels', [
        'key' => $label->getAttribute('key')
    ]);
});

it('translate a label', function () {
    $label = Models\Label::factory()->createOne();

    \PHPUnit\Framework\assertNotNull($label);

    \PHPUnit\Framework\assertInstanceOf(Models\Language::class, $this->locale);

    $translate = \Pest\Faker\faker()->sentence();

    $translation = Localization::translate($label, $translate, $this->locale->getAttribute('locale'));

    \PHPUnit\Framework\assertNotNull($translation);

    Assert\assertDatabaseHas('translations', [
        'label_id' => $label->getKey(),
        'language_id' => $this->locale->getKey()
    ]);
});

it('get translated labels', function ($locale) {
    $labels = Models\Label::factory()
        ->for(Models\Category::factory()->createOne())
        ->count(2)
        ->create();

    expect($labels)->toBeCollection();

    $translate = \Pest\Faker\faker()->sentence();

    Localization::translate($labels[0], $translate, $locale);
    Localization::translate($labels[1], $translate, $locale);

    $translatedLabels = Localization::translated($locale)
        ->map(
            static function ($category) {
                return array_keys($category->translated_labels);
            }
        )->all()[0];

    \PHPUnit\Framework\assertContains($labels[0]->getAttribute('key'), $translatedLabels);
    \PHPUnit\Framework\assertContains($labels[1]->getAttribute('key'), $translatedLabels);
})->with('locale');

it('get un-translated labels in specific locale', function ($translate, $locale) {
    $labels = Models\Label::factory()->count(2)->create();

    Localization::translate($labels[0], $translate, $locale);

    $notTranslated = Localization::notTranslated($locale);

    \PHPUnit\Framework\assertInstanceOf(Models\Language::class, $notTranslated);

    \PHPUnit\Framework\assertArrayHasKey('labels', $notTranslated->getAttributes());

    expect($notTranslated->getAttribute('labels'))->toBeCollection()->not->toBeEmpty();

    \PHPUnit\Framework\assertContains($labels[1]->id, $notTranslated->getAttribute('labels')->map(fn (Models\Label $label) => $label->getKey())->all());
})
    ->with('translate')
    ->with('locale');

it('get un-translated labels with specific category', function ($translate, $locale) {
    $category = Models\Category::factory()->createOne();

    $labels = Models\Label::factory()->for($category)->count(2)->create();

    expect($labels)->toBeCollection()->not->toBeEmpty();

    Localization::translate($labels[0], $translate, $locale);

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
