<?php

namespace consumer\components;

use yii\web\Controller;

class BaseController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array {
        return [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

}
