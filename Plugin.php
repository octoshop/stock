<?php namespace Octoshop\Stock;

use Backend;
use Event;
use Backend\Classes\FormTabs;
use Cart;
use Lang;
use Octoshop\Core\CartItem;
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

        $this->extendOctoshopCheckout();
    }

    protected function extendBackendForm()
    {
        Event::listen('backend.form.extendFields', function($widget) {
            if ($widget->getController() instanceof Products && $widget->model instanceof Product) {
                $qtyField = $widget->getField('minimum_qty');

                $widget->removeField('minimum_qty');

                $widget->addFields([
                    'is_stockable' => [
                        'label' => 'octoshop.stock::lang.product.isStockable',
                        'comment' => 'octoshop.stock::lang.product.isStockable_comment',
                        'type' => 'switch',
                        'span' => 'left',
                        'containerAttributes' => [
                            'style' => 'clear: both;',
                        ],
                    ],
                    'stock' => [
                        'label' => 'octoshop.stock::lang.product.stock',
                        'type' => 'number',
                        'span' => 'right',
                        'default' => 0,
                    ],
                    'reduce_stock' => [
                        'label' => 'octoshop.stock::lang.product.reduceStock',
                        'comment' => 'octoshop.stock::lang.product.reduceStock_comment',
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

    protected function extendOctoshopCheckout()
    {
        Event::listen('cart.validate_items', function() {
            Cart::registerItemValidator(function(CartItem $item) {
                $error = null;
                $product = $item->product();

                if ($product->isSoldOut()) {
                    $error = sprintf(Lang::get('octoshop.stock::lang.product.stock_empty'), $item->name);
                } elseif ($product->is_stockable && $product->stock < $item->qty) {
                    $error = sprintf(Lang::get('octoshop.stock::lang.product.stock_low'), $product->stock, $item->name);
                }

                return $error ?: true;
            });
        });

        Event::listen('octoshop.checkout.success', function($checkout) {
            foreach (Cart::content() as $item) {
                $product = $item->product();

                if ($product->reduce_stock) {
                    $product->stock -= $item->qty;
                    $product->save();
                }
            }
        });
    }
}
