<?php namespace Octoshop\Stock;

use Backend;
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
}
