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
        Schema::create($this->getTableName(Models\Translation::class), function (Blueprint $table) {
            $table->id();
            $table->mediumText('text')->nullable();
            $table->unsignedBigInteger('label_id');
            $table->unsignedBigInteger('language_id');
            $table->foreign('label_id')->on($this->getTableName(Models\Label::class))->references('id');
            $table->foreign('language_id')->on($this->getTableName(Models\Language::class))->references('id');
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
        Schema::dropIfExists('translations');
    }
};
