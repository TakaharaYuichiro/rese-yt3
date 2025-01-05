<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('users', function (Blueprint $table) {
        //     //
        // });

        // Laravel ロールの設定
        // https://qiita.com/yyy752/items/9f758a5266b2187179b2
        // roleカラムをTINYINT型でpasswordカラムの後に追加。更にインデックスを付与。
        // Schema::table('users', function (Blueprint $table) {
        //     $table->tinyInteger('role')->default(0)->after('password')->index('index_role')->comment('ロール');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropColumn('role');
        // });
    }
}
