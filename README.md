# Laravel Localization

At one of Bugloos's project, we should implement the multi-language feature, and the built-in Laravel localization
could not to any help us, because we need to some features that Laravel didn't have them, so we decide to implement amazing package.

### Features
In this package. you can have control on all your localization component such as :
1. Add category
2. Add label
3. Add language with self flag
4. Translate each label with chosen language
5. Migrate your self own translation files as: yaml, php, json , ...
6. Extract your translation data on database to chosen file as types as item 5

also if you want to add any other files type to item (5, 6) feature, you can configure it in configuration file at ``` config/localization.php``` .

### Usage
Laravel Localization make Facade as name **LocalizationFacade** for you to easy access to all features that was implemented.
#### 1. Add Category
```php
$category = \Bugloos\LaravelLocalization\Facades\LocalizationFacade::addCategory('messages');
```
#### 2. Add Label
```php
$label = \Bugloos\LaravelLocalization\Facades\LocalizationFacade::addLabel('error', $category);
```
Also, you can add new category inline if given #2 parameter string
```php
$label = \Bugloos\LaravelLocalization\Facades\LocalizationFacade::addLabel('text', 'info');
```
#### 3. Translate a Label
```php
\Bugloos\LaravelLocalization\Facades\LocalizationFacade::translate($label, 'some translation text ...', 'en');
```
But about locales, Laravel Localization has ```php Bugloos\LaravelLocalization\database\seeders\LanguageSeeder``` to fill your database with all locales and flags. just you should do activate each locale that you want.
<br/>

### Reporting
Also, Laravel Localization have 2 reporting function to let you get reporting of self your translation data :
#### 1. Translated
if you want to get all translated labels in specific locale :
```php
\Bugloos\LaravelLocalization\Facades\LocalizationFacade::translated('en');
```
Otherwise, return all categories with translated labels on each locale that is active :
```php
\Bugloos\LaravelLocalization\Facades\LocalizationFacade::translated();
```
#### 2. Not Translated
You can choose also locale and category or both them or none of them :
```php
\Bugloos\LaravelLocalization\Facades\LocalizationFacade::notTranslated(locale: 'en', category: 'messages');
```
<br/>

 ### Migrator
Sometimes you have own categorized translated labels or Front-End teams give it to you, and you should add these files or file to system, don't worry, Laravel Localization handle it for you easily with simple artisan command :

```php
php artisan localization:migrate path_your_files [--lang=en]
```

