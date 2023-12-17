<?php

/** @var yii\web\View $this */
/** @var common\models\User $user */

$verifyLink = sprintf('%s/reset-password?token=%s', Yii::$app->params['login_url'], $user->password_reset_token);
?>
Hello <?= $user->username ?>,

Follow the link below to verify your email:

<?= $verifyLink ?>
