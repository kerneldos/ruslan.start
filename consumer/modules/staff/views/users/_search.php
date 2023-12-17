<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\UserSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'username') ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'email') ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'status') ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'created_at') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
