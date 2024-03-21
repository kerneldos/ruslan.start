<?php
/** @var array $chartData */
?>

<!-- Custom tabs (Charts with tabs)-->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-pie mr-1"></i>
            Файлов по дате:
        </h3>
    </div><!-- /.card-header -->
    <div class="card-body">
        <div class="tab-content p-0">
            <canvas id="doc-by-date-chart-canvas" data-chart-data=<?= json_encode($chartData) ?> height="300" style="height: 300px;"></canvas>
        </div>
    </div><!-- /.card-body -->
</div>
<!-- /.card -->