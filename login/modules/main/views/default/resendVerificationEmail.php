<?php

/** @var yii\web\View$this  */
/** @var yii\bootstrap\ActiveForm $form */
/** @var ResetPasswordForm $model */

use login\models\ResetPasswordForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

$this->title = 'Please fill out your email. A verification email will be sent there.';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-resend-verification-email">
    <p><?= Html::encode($this->title) ?></p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'resend-verification-email-form']); ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
