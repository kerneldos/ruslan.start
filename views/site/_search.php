<?php

use app\models\DocumentSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var DocumentSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="document-search mb-5">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="input-group input-group-lg">
        <?= Html::activeInput('search', $model, 'content', [
            'class' => 'form-control form-control-lg',
            'placeholder' => 'Найти',
        ]) ?>

        <?php if (empty($model->content)): ?>
            <div class="input-group-append">
                <?= Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'btn btn-lg btn-default']) ?>
            </div>
        <?php else: ?>
            <div class="input-group-append">
                <a href="/site/index" class="btn btn-default"><i class="fa fa-times"></i></a>
            </div>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
