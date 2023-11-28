<?php

use consumer\models\Category;
use consumer\models\Document;
use consumer\models\DocumentSearch;
use consumer\models\Tag;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var DocumentSearch $model */
/** @var yii\widgets\ActiveForm $form */
/** @var Tag[] $tags */
?>

<div class="document-search mb-5">

    <?php $form = ActiveForm::begin([
        'action' => ['search'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mt-1">
                <div class="tree">
                    <label>Расположение:</label>
                    <div class="select">
                        <?= Html::activeHiddenInput($model, 'category') ?>
                        <span>
                            <?=
                                (!empty($model->category) ? Category::find()->select('name')->where(['id' => $model->category])->scalar() : '...')
                            ?>
                        </span>
                    </div>
                    <?php
                        function drawTree(array $tree): void {
                            echo '<ul>';
                                foreach ($tree as $node) {
                                    echo '<li>';
                                        echo '<a data-value="' . $node['id'] . '" href="#">' . $node['name'] . '</a>';

                                        if (!empty($node['children'])) {
                                            drawTree($node['children']);
                                        }
                                    echo '</li>';
                                }
                            echo '</ul>';
                        }

                        drawTree(Category::getTree());
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mt-1">
                <label>Типы документов:</label>
                <select class="select2" multiple="multiple" data-placeholder="..." style="width: 100%;" name="DocumentSearch[types][]">
                    <?php foreach (Document::TYPE_LIST as $type): ?>
                        <?php $selected = in_array($type, $_GET['DocumentSearch']['types'] ?? []) ? 'selected' : '' ?>
                        <option <?= $selected ?> value="<?= $type ?>"><?= $type ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mt-1">
                <label>Тэги:</label>
                <select class="select2" multiple="multiple" data-placeholder="..." style="width: 100%;" name="DocumentSearch[tags][]">
                    <?php foreach ($tags as $tag): ?>
                        <?php $selected = in_array($tag->name, $_GET['DocumentSearch']['tags'] ?? []) ? 'selected' : '' ?>
                        <option <?= $selected ?> value="<?= $tag->name ?>"><?= $tag->name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="input-group input-group-lg">
        <select style="width: 100%;" name="DocumentSearch[content]" id="" class="js-content-search"></select>
<!--        --><?php //= Html::activeInput('select', $model, 'content', [
//            'class' => 'js-content-search form-control form-control-lg',
////            'placeholder' => 'Найти',
//        ]) ?>

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
        <div class="form-check">
            <?= Html::activeInput('checkbox', $model, 'equalSearch', [
                'class' => 'form-check-input',
                'value' => 1,
                'checked' => !empty($model->equalSearch)
            ]) ?>
            <label class="form-check-label">Точное вхождение</label>
        </div>
    </div>

    <div class="form-group mt-1">
        <button type="submit" class="js-submit btn btn-primary">Найти</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
