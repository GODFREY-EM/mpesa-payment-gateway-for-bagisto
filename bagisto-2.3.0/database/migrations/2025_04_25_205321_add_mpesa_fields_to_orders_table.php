<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMpesaFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('mpesa_checkout_request_id')->nullable();
            $table->string('mpesa_receipt')->nullable();
            $table->string('mpesa_phone')->nullable();
            $table->decimal('mpesa_amount', 12, 4)->nullable();
            $table->string('mpesa_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'mpesa_checkout_request_id',
                'mpesa_receipt',
                'mpesa_phone',
                'mpesa_amount',
                'mpesa_status'
            ]);
        });
    }
}