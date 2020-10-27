<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateLikeableTables
 */
class CreateLikeableTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
	{
		Schema::create('likes', function(Blueprint $table) {
            $table->id();
			$table->morphs('likeable');
			$table->unsignedBigInteger('user_id')->index();
            $table->enum('type_id', [
                'like',
                'dislike',
            ])->default('like');
			$table->timestamps();

            $table->unique([
                'likeable_id',
                'likeable_type',
                'user_id',
            ], 'like_user_unique');

		});

		Schema::create('like_counters', function(Blueprint $table) {
			$table->id();
			$table->morphs('likeable');
            $table->enum('type_id', [
                'like',
                'dislike',
            ])->default('like');
			$table->unsignedBigInteger('count')->default(0);
			$table->timestamps();

            $table->unique([
                'likeable_id',
                'likeable_type',
                'type_id',
            ], 'like_counter_unique');
		});

	}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
	{
		Schema::drop('likes');
		Schema::drop('like_counters');
	}
}
