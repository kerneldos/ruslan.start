<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap\ActiveForm $form */
/** @var consumer\models\LoginForm $model */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card-body login-card-body">
    <p class="login-box-msg">Sign in to start your session</p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
    ]); ?>
        <div class="input-group mb-3">
            <?= Html::activeTextInput($model, 'username', ['placeholder' => 'Login', 'class' => 'form-control']) ?>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        <div class="input-group mb-3">
            <?= Html::activePasswordInput($model, 'password', ['placeholder' => 'Password', 'class' => 'form-control']) ?>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                    <?= Html::activeCheckbox($model, 'rememberMe') ?>
                    <label for="remember">
                        Remember Me
                    </label>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
            <!-- /.col -->
        </div>
    <?php ActiveForm::end(); ?>

    <p class="mb-1">
        <?= Html::a('I forgot my password', Url::to(['request-password-reset'])) ?>
    </p>
    <p class="mb-0">
        <a href="/signup" class="text-center">Register a new membership</a>
    </p>
</div>
<!-- /.login-card-body -->
