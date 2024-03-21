<?php

namespace consumer\modules\dashboard\widgets\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ToDoListAsset extends AssetBundle
{
    public $sourcePath = __DIR__;

    public $css = [];

    public $js = [
        'js/to-do-list.js',
    ];

    public $depends = [
        'common\assets\AppAsset',
    ];
}