<?php

namespace consumer\modules\dashboard\controllers;

use yii\web\Controller;

/**
 * Default controller for the `dashboard` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex(): string {
        $widgets = [
            'consumer\modules\dashboard\widgets\DocumentsByDate',
            'consumer\modules\dashboard\widgets\ToDoList',
            'consumer\modules\dashboard\widgets\DocumentsByType',
            'consumer\modules\dashboard\widgets\DocumentsByCategory',
            'consumer\modules\dashboard\widgets\Calendar',
        ];

        return $this->render('index', ['widgets' => $widgets]);
    }
}
