<?php

return [
    'plugin' => [
        'name' => 'Octoshop Stock',
        'description' => 'Добавьте способность отследить запас в Вашем магазине Octoshop.',
    ],
    'product' => [
        'isStockable' => 'ЗАСТАВЛЯТЬ ПОДВОЙ УРОВЕНЬ',
        'isStockable_comment' => "Средства управления, могут ли клиенты купить продукт, когда это отсутствует на складе.",
        'stock' => 'Единицы в запасе',
        'stock_low' => 'Есть только %s ИЗ "%s", оставленного в запасе.',
        'stock_empty' => '"%s" распродан.',
        'reduceStock' => 'УМЕНЬШАТЬ ПОДВОЙ',
        'reduceStock_comment' => 'С этим позволенным выбором уровень запасов будет уменьшен когда потребительский контроль.',
    ],
];
