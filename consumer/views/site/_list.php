<?php

use consumer\models\Document;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var Document $model */

$highlight = '';
if (!empty($model->highlight['attachment.content'])) {
    $highlight = join(' ', $model->highlight['attachment.content']);
} elseif (!empty($model->highlight['content'])) {
    $highlight = join(' ', $model->highlight['content']);
}

?>

<div class="list-group-item my-1">
    <div class="row">
        <div class="col px-4">
            <div>
                <div class="float-right"><?= date('d-m-Y-H-i-s', (int) $model->created) ?></div>
                <h3><?= Html::a($model->name, Url::toRoute(['view', 'id' => $model->_id])) ?></h3>
                <p class="mb-0"><?= $highlight ?></p>
            </div>
        </div>
    </div>
</div>
