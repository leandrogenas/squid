<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinkLoadBalancersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_load_balancers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("link_original");
            $table->string("link_redirect");
            $table->string("link_server");
            $table->dateTime("expira_em");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('link_load_balancers');
    }
}
