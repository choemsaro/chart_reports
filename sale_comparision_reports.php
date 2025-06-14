<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<h2 style=" text-align: center;"><?= lang('Sales Comparison Chart') ?></h2>
<canvas id="salesComparisonChart" width="70%" height="60"></canvas>
<hr>
<?php foreach (['m0' => 'ខែនេះ', 'm1' => 'ខែមុន', 'm2' => '២ខែមុន'] as $key => $label): ?>
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

<script>
    const ctx = document.getElementById('salesComparisonChart').getContext('2d');
    const labels = <?= json_encode([$m2, $m1, $m0]) ?>;
    const salesData = <?= json_encode([
        array_sum(array_column($m2bs, 'total')),
        array_sum(array_column($m1bs, 'total')),
        array_sum(array_column($m0bs, 'total'))
    ]) ?>;
    

    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Sales ($)',
                data: salesData,
                backgroundColor: '#3A87AD',
                borderRadius: 5,
                barThickness: 60
            }]
        },
        options: {
            responsive: true,
            plugins: {
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    formatter: (value) => '$' + parseFloat(value).toLocaleString(),
                    color: '#000',
                    font: {
                        weight: 'bold'
                    }
                },
                title: {
                    display: true,
                    text: 'Total Sales (Last 3 Months)'
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return '$' + parseFloat(context.raw).toLocaleString();
                        }
                    }
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return '$' + value.toLocaleString();
                        }
                    },
                    title: {
                        display: true,
                        text: 'Total Amount ($)'
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>