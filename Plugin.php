<?php namespace Octoshop\Stock;

use Backend;
use Event;
use Backend\Classes\FormTabs;
use Octoshop\Core\Controllers\Products;
use Octoshop\Core\Models\Product;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = ['Octoshop.Core'];

    public function pluginDetails()
    {
        return [
            'name' => 'octoshop.stock::lang.plugin.name',
            'icon' => 'icon-shopping-cart',
            'author' => 'Dave Shoreman',
            'homepage' => 'http://octoshop.co/',
            'description' => 'octoshop.stock::lang.plugin.description',
        ];
    }

    public function boot()
    {
        $this->extendBackendForm();
        $this->extendModels();
    }

    protected function extendBackendForm()
    {
        Event::listen('backend.form.extendFields', function($widget) {
            if ($widget->getController() instanceof Products && $widget->model instanceof Product) {
                $qtyField = $widget->getField('minimum_qty');

                $widget->removeField('minimum_qty');

                $widget->addFields([
                    'is_stockable' => [
                        'label' => 'Enforce Stock Levels',
                        'comment' => 'Controls whether customers can purchase a product when it\'s out of stock.',
                        'type' => 'switch',
                        'span' => 'left',
                        'containerAttributes' => [
                            'style' => 'clear: both;',
                        ],
                    ],
                    'stock' => [
                        'label' => 'Units in Stock',
                        'type' => 'number',
                        'span' => 'right',
                        'default' => 0,
                    ],
                    'reduce_stock' => [
                        'label' => 'Reduce Stock',
                        'comment' => 'With this option enabled, the stock level will be reduced when customers checkout.',
                        'type' => 'switch',
                        'span' => 'left',
                    ],
                    'minimum_qty' => $qtyField->config,
                ], FormTabs::SECTION_SECONDARY);
            }
        });
    }

    protected function extendModels()
    {
        Product::extend(function($model) {
            $model->addDynamicMethod('isInStock', function() use ($model) {
                if (!$model->is_stockable) {
                    return true;
                }

                return $model->stock >= $model->minimum_qty;
            });

            $model->addDynamicMethod('isNotInStock', function() use ($model) {
                return !$model->isInStock();
            });

            $model->addDynamicMethod('isSoldOut', function() use ($model) {
                return $model->isNotInStock();
            });
        });
    }
}
