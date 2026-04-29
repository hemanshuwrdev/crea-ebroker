<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddInvoiceNoToPaymentTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('invoice_no')->nullable()->unique()->after('transaction_id');
        });

        $successPayments = DB::table('payment_transactions')
            ->where('payment_status', 'success')
            ->orderBy('id')
            ->get();

        $counter = 0;
        foreach ($successPayments as $payment) {
            $counter++;
            DB::table('payment_transactions')
                ->where('id', $payment->id)
                ->update(['invoice_no' => str_pad($counter, 3, '0', STR_PAD_LEFT)]);
        }
    }

    public function down()
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropUnique(['invoice_no']);
            $table->dropColumn('invoice_no');
        });
    }
}
