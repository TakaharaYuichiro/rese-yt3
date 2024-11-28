<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::create('evaluations', function (Blueprint $table) {
            
            // LaravelのEloquentを使って複合主キーを使用したテーブルを操作する方法
            // https://qiita.com/wrbss/items/7245103a5fef88cbdde9

            // // $table->id();
            // $table->integer('user_id')->unsigned();
            // $table->integer('shop_id')->unsigned();
            // $table->timestamps();

            //  // 外部キー設定
            //  $table->foreign('user_id')->references('id')->on('users');
            //  $table->foreign('shop_id')->references('id')->on('shops');
             
            //  // プライマリキー設定
            //  $table->unique(['user_id', 'shop_id']);


            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->boolean('favorite');
            $table->tinyInteger('score');
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('evaluations');
    }
}
