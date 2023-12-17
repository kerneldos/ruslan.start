<?php

namespace common\rbac;

use Yii;
use yii\helpers\ArrayHelper;
use yii\rbac\Rule;

class UserPortalRule extends Rule {
    public $name = 'userPortal';

    /**
     * @inheritDoc
     */
    public function execute($user, $item, $params): bool {
        if (!Yii::$app->user->isGuest) {
            return in_array(Yii::$app->params['subDomain'], ArrayHelper::getColumn(Yii::$app->user->identity->portals, 'temp_name'));
        }

        return false;
    }
}