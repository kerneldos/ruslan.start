<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap\ActiveForm $form */
/** @var consumer\models\LoginForm $model */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = 'Sign in to start your session';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card-body login-card-body">
    <p><?= Html::encode($this->title) ?></p>

    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
        <div class="row">
            <div class="input-group mb-3 col-12">
                <?php $error = $model->hasErrors('username'); ?>

                <?= Html::activeTextInput($model, 'username', ['placeholder' => 'Login', 'class' => 'form-control']) ?>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
                <?php if ($error): ?>
                    <span class="error invalid-feedback"><?= join(' ', $model->errors['username']) ?></span>
                <?php endif; ?>
            </div>

            <div class="input-group mb-3 col-12">
                <?php $error = $model->hasErrors('password'); ?>

                <?= Html::activePasswordInput($model, 'password', [
                    'placeholder' => 'Password',
                    'class' => 'form-control ' . ($error ? 'is-invalid' : ''),
                ]) ?>

                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>

                <?php if ($error): ?>
                    <span class="error invalid-feedback"><?= join(' ', $model->errors['password']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                    <?= Html::activeCheckbox($model, 'rememberMe', ['label' => false]) ?>
                    <label for="remember">
                        Remember Me
                    </label>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
                <?= Html::submitButton('Sign In', ['class' => 'btn btn-primary btn-block']) ?>
            </div>
            <!-- /.col -->
        </div>
    <?php ActiveForm::end(); ?>

    <p class="mb-1">
        <?= Html::a('I forgot my password', Url::to(['request-password-reset'])) ?>
    </p>
    <p class="mb-0">
        <?= Html::a('Register a new membership', Url::to(['signup'])) ?>
    </p>
</div>
<!-- /.login-card-body -->
