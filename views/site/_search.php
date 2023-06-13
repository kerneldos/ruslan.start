<?php

use app\models\DocumentSearch;
use app\models\Tag;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var DocumentSearch $model */
/** @var yii\widgets\ActiveForm $form */
/** @var Tag[] $tags */
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

    <div class="form-group mt-1">
        <label>Тэги</label>
        <select class="select2" multiple="multiple" data-placeholder="..." style="width: 100%;" name="DocumentSearch[tags][]">
            <?php foreach ($tags as $tag): ?>
                <?php $selected = in_array($tag->name, $_GET['DocumentSearch']['tags'] ?? []) ? 'selected' : '' ?>
                <option <?= $selected ?> value="<?= $tag->name ?>"><?= $tag->name ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group mt-1">
        <button type="submit" class="js-submit btn btn-primary">Найти</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
