<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_feeds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("link");
            $table->string("titulo");
            $table->string("tipo");
            //ALTER TABLE `list_feeds` ADD `tipo_link` VARCHAR(191) NULL DEFAULT NULL AFTER `tipo`;
            //update list_feeds set tipo_link = "Nimbus" where link like "%nimbus%";
            //update list_feeds set tipo_link = "Google Drive" where link like "%docs.google%";
            //update list_feeds set tipo_link = "Twitter" where link like "%twitter%";
            //update list_feeds set tipo_link = "Evernote" where link like "%evernote%";
            //update list_feeds set tipo_link = "Feed" where tipo_link is null;
            $table->string("tipo_link");
            $table->string("status")->default(\App\Enums\FeedLinkStatus::NAO_ENVIADO);
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
        Schema::dropIfExists('list_feeds');
    }
}
