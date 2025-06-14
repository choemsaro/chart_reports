<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<h2><?= lang('Sales Comparison Chart') ?></h2>
<canvas id="salesComparisonChart" width="100%" height="50"></canvas>

<hr>
<?php foreach (['m0' => 'This Month', 'm1' => 'Last Month', 'm2' => 'Two Months Ago'] as $key => $label): ?>
    <h4><?= $label ?>: <?= ${$key} ?></h4>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th><?= lang('Product Name') ?></th>
                <th><?= lang('Quantity') ?></th>
                <th><?= lang('Total') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (${$key . 'bs'} as $row): ?>
                <tr>
                    <td><?= $row->product_name ?></td>
                    <td><?= $row->qty ?></td>
                    <td><?= $this->sma->formatMoney($row->total) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endforeach; ?>

<!-- Chart Data Script -->
<script>
    const labels = <?= json_encode([$m2, $m1, $m0]) ?>;

    // Build product dataset
    const products = {};
    <?php
    $monthKeys = ['m2bs', 'm1bs', 'm0bs'];
    foreach ($monthKeys as $monthIndex => $key) {
        foreach ($$key as $item) {
            $name = $item->product_name;
            if (!isset($products[$name])) {
                $products[$name] = [0, 0, 0]; // for 3 months
            }
            $products[$name][$monthIndex] = (float) $item->qty;
        }
    }
    ?>

    const chartData = {
        labels: labels,
        datasets: [
            <?php
            $colors = ['#FF6384', '#36A2EB', '#4BC0C0', '#9966FF', '#FF9F40', '#8B0000', '#006400'];
            $i = 0;
            foreach ($products as $name => $qtys): ?>
                    {
                    label: <?= json_encode($name) ?>,
                    data: <?= json_encode(array_reverse($qtys)) ?>,
                    fill: false,
                    borderColor: '<?= $colors[$i % count($colors)] ?>',
                    backgroundColor: '<?= $colors[$i % count($colors)] ?>',
                    tension: 0.3
                },
                <?php $i++; endforeach; ?>
        ]
    };

    const config = {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Sales Quantity Comparison (3 Months)'
                },
                legend: {
                    position: 'bottom'
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantity Sold'
                    }
                }
            }
        }
    };
    new Chart(document.getElementById('salesComparisonChart'), config);
</script>