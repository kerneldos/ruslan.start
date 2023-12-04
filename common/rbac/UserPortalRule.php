<?php

namespace common\rbac;

use common\models\Portal;
use Yii;
use yii\rbac\Rule;

class UserPortalRule extends Rule {
    public $name = 'userPortal';

    /**
     * @inheritDoc
     */
    public function execute($user, $item, $params): bool {
        if (!Yii::$app->user->isGuest) {
            $userPortals = Portal::find()->select('temp_name')->where(['user_id' => $user])->column();

            return in_array(Yii::$app->params['subDomain'], $userPortals);
        }

        return false;
    }
}