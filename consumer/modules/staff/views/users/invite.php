<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap\ActiveForm $form */
/** @var InviteForm $model */

use consumer\modules\staff\models\InviteForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

$this->title = 'Invite users:';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="staff-users-invite">
    <div class="row">
        <div class="col-sm-12 mb-3">
            <p><?= Html::encode($this->title) ?></p>

            <?= Html::a('Пользователи', ['index'], [
                'class' => 'btn btn-primary',
            ]) ?>
        </div>
    </div>

    <?php $form = ActiveForm::begin(['id' => 'invite-form']); ?>
    <div class="row">
        <div class="input-group mb-3 col-4">
            <?php $error = $model->hasErrors('email'); ?>

            <?= Html::activeTextInput($model, 'email', [
                'placeholder' => 'Email',
                'class' => 'form-control ' . ($error ? 'is-invalid' : ''),
            ]) ?>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>

            <?php if ($error): ?>
                <span class="error invalid-feedback"><?= join(' ', $model->errors['email']) ?></span>
            <?php endif; ?>
        </div>

        <div class="col-12">
            <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
