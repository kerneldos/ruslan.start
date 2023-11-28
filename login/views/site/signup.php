<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap\ActiveForm $form */
/** @var SignupForm $model */

use login\models\SignupForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card-body register-card-body">
    <p class="login-box-msg">Register a new membership</p>

    <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
        <div class="input-group mb-3">
            <?= Html::activeInput('text', $model, 'username', ['class' => 'form-control', 'placeholder' => 'Login']) ?>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>
        <div class="input-group mb-3">
            <?= Html::activeInput('email', $model, 'email', ['class' => 'form-control', 'placeholder' => 'Email']) ?>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        <div class="input-group mb-3">
            <?= Html::activeInput('password', $model, 'password', ['class' => 'form-control', 'placeholder' => 'Password']) ?>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                    <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                    <label for="agreeTerms">
                        I agree to the <a href="#">terms</a>
                    </label>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
                <?= Html::submitButton('Register', ['class' => 'btn btn-primary btn-block', 'name' => 'signup-button']) ?>
            </div>
            <!-- /.col -->
        </div>
    <?php ActiveForm::end(); ?>

    <div class="social-auth-links text-center">
        <p>- OR -</p>
        <a href="#" class="btn btn-block btn-primary">
            <i class="fab fa-facebook mr-2"></i>
            Sign up using Facebook
        </a>
        <a href="#" class="btn btn-block btn-danger">
            <i class="fab fa-google-plus mr-2"></i>
            Sign up using Google+
        </a>
    </div>

    <a href="/" class="text-center">I already have a membership</a>
</div>
