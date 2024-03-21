<?php

/** @var yii\web\View $this */
/** @var array $widgets */

$this->title = 'Search Project';
?>
    <div class="site-index">
        <div class="container-fluid">
            <!-- Main row -->
            <div class="row connectedSortable">
                <?php foreach ($widgets as $key => $widgetClass): ?>
                    <section class="col-lg-4" id="module<?= $key ?>">
                        <?= $widgetClass::widget() ?>
                    </section>
                <?php endforeach; ?>
            </div>
            <!-- /.row (main row) -->
        </div>
    </div>

    <style>
        /* Re-order items into 3 rows */
        .connectedSortable .card {
            height: 400px;
        }
    </style>

<?php
$js = <<<JS
    // Make the dashboard widgets sortable Using jquery UI
    $('.connectedSortable').sortable({
        placeholder: 'sort-highlight',
        connectWith: '.connectedSortable',
        handle: '.card-header, .nav-tabs',
        forcePlaceholderSize: true,
        zIndex: 999999,
        animation: 200,
        revert: true,
        update: function( event, ui ) {
          console.log($(this).sortable('toArray'));
        }
    })
    $('.connectedSortable .card-header').css('cursor', 'move')

    $('[data-card-widget="collapse"]').on('click', function () {
        let card = $(this).closest('.card');

        if (!card.is('.collapsed-card')) {
            card.css('height', 'auto');
        } else {
            card.removeAttr('style');
        }
    })
JS;

$this->registerJs($js);
