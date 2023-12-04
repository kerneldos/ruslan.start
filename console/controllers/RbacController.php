<?php

namespace console\controllers;

use common\rbac\UserPortalRule;
use Exception;
use Yii;
use yii\console\Controller;

class RbacController extends Controller {
    /**
     * @return void
     * @throws Exception
     */
    public function actionIndex() {
        $auth = Yii::$app->authManager;

        // add the rule
        $rule = new UserPortalRule();
        $auth->add($rule);

        $member = $auth->createRole('portal_member');
        $member->ruleName = $rule->name;
        $auth->add($member);
    }
}