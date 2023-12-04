<?php

namespace login\components;

use yii\base\Module;

class BaseModule extends Module {
    /**
     * @return array[]
     */
    public function behaviors(): array {
        return [
//            'access' => [
//                'class' => 'yii\filters\AccessControl',
//                'rules' => [
//                    [
//                        'allow' => true,
//                        'roles' => ['isPortalMember'],
//                    ],
//                ],
//            ],
        ];
    }
}