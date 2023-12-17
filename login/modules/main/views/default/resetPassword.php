<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap\ActiveForm $form */
/** @var ResetPasswordForm $model */

use login\models\ResetPasswordForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

$this->title = 'Reset password';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-reset-password card-body login-card-body">
    <p><?= Html::encode($this->title) ?></p>

    <p>Please choose your new password:</p>

    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
        <div class="row">
            <div class="input-group mb-3 col-12">
                <?= Html::activeInput('password', $model, 'password', ['class' => 'form-control', 'placeholder' => 'Password']) ?>

                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
