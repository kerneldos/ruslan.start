<?php

namespace consumer\modules\dashboard\widgets;

use consumer\modules\dashboard\widgets\assets\ToDoListAsset;
use yii\base\Widget;

class ToDoList extends Widget {
    /**
     * @return string
     */
    public function run(): string {
        ToDoListAsset::register($this->getView());

        return $this->render('to-do-list');
    }
}