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

    <div class="input-group input-group-lg">
        <?= Html::activeInput('search', $model, 'content', [
            'class' => 'form-control form-control-lg',
            'placeholder' => 'Найти',
        ]) ?>
        <div class="input-group-append">
            <?= Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-lg btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
