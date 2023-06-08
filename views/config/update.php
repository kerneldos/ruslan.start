<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Config $model */

$this->title = 'Update Config: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="config-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
