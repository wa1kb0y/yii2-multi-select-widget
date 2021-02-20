<?php

namespace dosamigos\multiselect;

use yii\web\AssetBundle;

class QuicksearchAsset extends AssetBundle
{
    public $sourcePath = '@bower/quicksearch/dist';

    public $js = [
        'jquery.quicksearch.min.js'
    ];

    public $css = [];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
