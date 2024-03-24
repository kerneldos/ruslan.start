<?php
/** @var array $chartData */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-pie mr-1"></i>
            Файлов по типу:
        </h3>
    </div><!-- /.card-header -->
    <div class="card-body">
        <div class="tab-content p-0 chart-container">
            <canvas data-chart-data='<?= json_encode($chartData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>' height="300" style="height: 300px;"></canvas>
        </div>
    </div><!-- /.card-body -->
</div>