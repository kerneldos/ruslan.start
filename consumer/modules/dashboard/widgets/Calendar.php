<?php

namespace consumer\modules\dashboard\widgets;

use consumer\modules\dashboard\widgets\assets\CalendarAsset;
use yii\base\Widget;

class Calendar extends Widget {
    /**
     * @return string
     */
    public function run(): string {
        CalendarAsset::register($this->getView());

        return $this->render('calendar');
    }
}