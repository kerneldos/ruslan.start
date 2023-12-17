<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\User $user */

$verifyLink = sprintf('%s/reset-password?token=%s', Yii::$app->params['login_url'], $user->password_reset_token);
?>
<div class="verify-email">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p>Follow the link below to verify your email:</p>

    <p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>
</div>
