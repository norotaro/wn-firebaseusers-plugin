<?php

namespace Norotaro\FirebaseUsers\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class Migration102 extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('fb_uid', 50)->nullable()->unique();
            $table->boolean('fb_sync', 50)->default(false);
            $table->string('email')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('fb_uid');
            $table->dropColumn('fb_sync');
            $table->string('email')->nullable(false)->change();
        });
    }
}
