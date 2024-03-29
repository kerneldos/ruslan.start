<?php

namespace consumer\modules\dashboard\widgets\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CalendarAsset extends AssetBundle
{
    public $sourcePath = __DIR__;

    public $css = [];

    public $js = [
        'js/calendar.js',
    ];

    public $depends = [
        'common\assets\AppAsset',
    ];
}