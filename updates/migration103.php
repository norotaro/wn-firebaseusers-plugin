<?php

namespace Norotaro\FirebaseUsers\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class Migration103 extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->dropUnique(['email']);
        });
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->unique('email');
        });
    }
}
