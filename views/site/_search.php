<?php

use yii\base\Model;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var Model $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="document-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'content')->label('Поиск:') ?>

    <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>

    <?php ActiveForm::end(); ?>

</div>
