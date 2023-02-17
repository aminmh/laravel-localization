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
        Schema::create($this->getTable(Models\Language::class), function (Blueprint $table) {
            $table->id();
            $table->char('locale', 10); // because always length is 10 character
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('name', 70);
            $table->boolean('active')->default(false);

            $table->foreign('country_id')->references('id')->on($this->getTable(Models\Country::class))->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->getTable(Models\Language::class));
    }
};
