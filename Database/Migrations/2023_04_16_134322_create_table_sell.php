<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSell extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sell_factors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fake_id')->nullable();
            $table->unsignedBigInteger('factor_id')->unique();
            $table->unsignedBigInteger('pre_invocie_id')->nullable();
            $table->foreignId('personal_id')->references('id')->on('personals');
            $table->date('action_date');
            $table->unsignedBigInteger('tax')->default(0);
            $table->unsignedBigInteger('discount')->default(0);
            $table->unsignedBigInteger('price');
            $table->string('intro')->nullable();
            $table->foreignId('fiscal_year_id')->references('id')->on('fiscal_years');
            $table->foreignId('company_id')->references('id')->on('companies');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->enum('status', ['pending', 'draft', 'published'])->default('pending');
            $table->boolean('is_pos')->default(0);
            $table->boolean('is_cash_desc')->default(0);
            $table->timestamps();
        });
        Schema::create('sell_factor_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factor_id')->references('id')->on('sell_factors');
            $table->foreignId('product_id')->references('id')->on('products');
            $table->unsignedInteger('count');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedBigInteger('discount')->default(0);
            $table->unsignedBigInteger('total_price');
            $table->foreignId('warehouse_id')->references('id')->on('warehouses');
            $table->string('intro')->nullable();
            $table->foreignId('fiscal_year_id')->references('id')->on('fiscal_years');
            $table->foreignId('company_id')->references('id')->on('companies');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->enum('status', ['pending', 'draft', 'published'])->default('pending');
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
        Schema::dropIfExists('sell_factors');
        Schema::dropIfExists('sell_factor_details');
    }
}
