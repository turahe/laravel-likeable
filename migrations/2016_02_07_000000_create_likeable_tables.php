<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikeableTables extends Migration
{
	public function up()
	{
		Schema::create('likes', function(Blueprint $table) {
            $table->id();
			$table->morphs('likeable');
			$table->unsignedBigInteger('user_id')->index();
			$table->timestamps();
		});

		Schema::create('like_counters', function(Blueprint $table) {
			$table->id();
			$table->morphs('likeable');
			$table->unsignedBigInteger('count')->default(0);
			$table->timestamps();
		});

	}

	public function down()
	{
		Schema::drop('likes');
		Schema::drop('like_counters');
	}
}
