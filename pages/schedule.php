<?php
$metaTitle = 'Program Schedule - EKO FM';
$items = programs_list(true);
?>
<main class="container-xxl py-4">
    <div class="mb-4 reveal">
        <h1 class="mb-1">Program Schedule</h1>
        <p class="text-muted mb-0">Daily and weekly lineup of your favorite EKO FM programs.</p>
    </div>

    <section class="section-card reveal reveal-delay-1">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="chip">Morning</span>
            <span class="chip">Afternoon</span>
            <span class="chip">Evening</span>
            <span class="chip">Weekend</span>
        </div>

        <div class="table-responsive">
            <table class="table align-middle schedule-table mb-0">
                <thead>
                    <tr>
                        <th>Show</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Presenter</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $row): ?>
                    <tr>
                        <td><a href="<?php echo e(url('shows/' . $row['slug'])); ?>" data-pjax><?php echo e($row['title']); ?></a></td>
                        <td><?php echo e($row['day_of_week']); ?></td>
                        <td><span class="show-time"><?php echo e(substr($row['start_time'], 0, 5)); ?> - <?php echo e(substr($row['end_time'], 0, 5)); ?></span></td>
                        <td><?php echo e($row['presenter']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
