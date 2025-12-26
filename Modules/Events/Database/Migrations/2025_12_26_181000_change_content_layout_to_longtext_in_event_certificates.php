<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('event_certificates', function (Blueprint $table) {
            $table->longText('content_layout')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('event_certificates', function (Blueprint $table) {
            $table->string('content_layout')->change();
        });
    }
};
