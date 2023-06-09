<?php

use app\models\Document;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var Document $model */
?>

<div class="list-group-item">
    <div class="row">
        <div class="col px-4">
            <div>
                <div class="float-right"><?= date('d-m-Y-H-i-s', (int) $model->created) ?></div>
                <h3><?= Html::a($model->name, Url::toRoute(['view', 'id' => $model->_id])) ?></h3>
                <p class="mb-0"><?= !empty($model->highlight['content']) ? join(' ', $model->highlight['content']) : '' ?></p>
            </div>
        </div>
    </div>
</div>
