<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var consumer\models\Config $model */

$this->title = 'Create Config';
$this->params['breadcrumbs'][] = ['label' => 'Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
