<?php

use Bugloos\LaravelLocalization\Models;
use Bugloos\LaravelLocalization\Traits\ConfiguredTableName;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    use ConfiguredTableName;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->getTable(Models\Label::class), function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unique(['key', 'category_id']);
            $table->foreign('category_id')->on($this->getTable(Models\Category::class))->references('id')->cascadeOnDelete();
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
        Schema::dropIfExists($this->getTable(Models\Label::class));
    }
};
