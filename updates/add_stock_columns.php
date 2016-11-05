<?php namespace Octoshop\Stock\Updates;

use Schema;
use Octoshop\Core\Updates\Migration;

class AddStockColumns extends Migration
{
    public function up()
    {
        Schema::table('octoshop_products', function ($table) {
            $table->boolean('is_stockable')->default(false);
            $table->boolean('reduce_stock')->default(false);
            $table->integer('stock')->default(0)->nullable();
        });
    }

    public function down()
    {
        foreach (['stock', 'reduce_stock', 'is_stockable'] as $column) {
            if (!Schema::hasColumn('octoshop_products', $column)) {
                continue;
            }

            Schema::table('octoshop_products', function($table) use ($column) {
                $table->dropColumn($column);
            });
        }
    }
}
