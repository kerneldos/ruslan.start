<?php
/** @var array $data */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <img src="<?= $data['channel']['image']['url'] ?>" alt="" style="height: 22px;">
            Новости:
        </h3>
    </div><!-- /.card-header -->
    <div class="card-body overlay-scrollbar">
        <?php foreach ($data['channel']['item'] as $key => $item): ?>
            <div class="callout callout-info">
                <h5><a href="<?= $item['link'] ?>" target="_blank"><?= $item['title'] ?></a></h5>

                <p>
                    <img src="<?= $item['enclosure']['@attributes']['url'] ?>" alt="" style="height: 120px;float: <?= ($key % 2 == 0 ? 'left' : 'right') ?>;margin-<?= ($key % 2 == 0 ? 'right' : 'left') ?>: 10px;">
                    <?= $item['description'] ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div><!-- /.card-body -->
</div>