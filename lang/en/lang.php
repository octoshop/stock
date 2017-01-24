<?php

return [
    'plugin' => [
        'name' => 'Octoshop Stock',
        'description' => 'Add the ability to track stock in your Octoshop store.',
    ],
    'product' => [
        'isStockable' => 'Enforce Stock Levels',
        'isStockable_comment' => "Controls whether customers can purchase a product when it's out of stock.",
        'stock' => 'Units in Stock',
        'stock_low' => 'There are only %s of "%s" left in stock.',
        'stock_empty' => '"%s" is sold out.',
        'reduceStock' => 'Reduce Stock',
        'reduceStock_comment' => 'With this option enabled, the stock level will be reduced when customers checkout.',
    ],
];
