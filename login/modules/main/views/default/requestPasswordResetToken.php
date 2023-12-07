<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap\ActiveForm $form */
/** @var PasswordResetRequestForm $model */

use login\models\PasswordResetRequestForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = 'Please fill out your email. A link to reset password will be sent there.';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset card-body login-card-body">
    <p><?= Html::encode($this->title) ?></p>

    <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
        <div class="row">
            <div class="input-group mb-3 col-12">
                <?= Html::activeTextInput($model, 'email', ['placeholder' => 'Email', 'class' => 'form-control']) ?>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>

    <?= Html::a('Go to Login Page', Url::to(['login'], ['class' => 'text-center'])) ?>
</div>
