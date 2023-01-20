<?php

use Bugloos\LaravelLocalization\Models;
use Bugloos\LaravelLocalization\Traits\ConfiguredTableName;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    use ConfiguredTableName;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->getTableName(Models\Language::class), function (Blueprint $table) {
            $table->id();
            $table->char('locale', 3); // because always length is 2 character
            $table->string('locale_name');
            $table->string('country');
            $table->string('country_name');
            $table->boolean('active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
    }
};
