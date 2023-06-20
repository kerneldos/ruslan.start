<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Config $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="config-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList($model::TYPES_MAP) ?>

    <?php if (!empty($model->type)): ?>
        <?php if ($model->type == $model::TYPE_TEXT): ?>
            <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
        <?php elseif ($model->type == $model::TYPE_TEXTAREA): ?>
            <?= $form->field($model, 'value')->textarea() ?>
        <?php elseif ($model->type == $model::TYPE_PASSWORD): ?>
            <?= $form->field($model, 'value')->passwordInput() ?>
        <?php endif; ?>
    <?php else: ?>
        <?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
