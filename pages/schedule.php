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
            <button type="button" class="chip filter-chip" data-filter-group="time" data-filter-value="morning">Morning</button>
            <button type="button" class="chip filter-chip" data-filter-group="time" data-filter-value="afternoon">Afternoon</button>
            <button type="button" class="chip filter-chip" data-filter-group="time" data-filter-value="evening">Evening</button>
            <button type="button" class="chip filter-chip" data-filter-group="day" data-filter-value="weekdays">Weekdays</button>
            <button type="button" class="chip filter-chip" data-filter-group="day" data-filter-value="weekend">Weekend</button>
            <button type="button" class="chip filter-chip" id="scheduleClearFilters">Clear</button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle schedule-table mb-0">
                <thead>
                    <tr>
                        <th>Show</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Presenter</th>
                        <th>Tones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $row): ?>
                    <?php
                    $startHour = (int) substr((string) $row['start_time'], 0, 2);
                    $timeBucket = 'evening';
                    if ($startHour < 12) {
                        $timeBucket = 'morning';
                    } elseif ($startHour < 17) {
                        $timeBucket = 'afternoon';
                    }

                    $dayText = strtolower((string) $row['day_of_week']);
                    $isWeekend = strpos($dayText, 'saturday') !== false || strpos($dayText, 'sunday') !== false || strpos($dayText, 'daily') !== false || strpos($dayText, 'every') !== false;
                    $isWeekday = strpos($dayText, 'monday') !== false || strpos($dayText, 'tuesday') !== false || strpos($dayText, 'wednesday') !== false || strpos($dayText, 'thursday') !== false || strpos($dayText, 'friday') !== false || strpos($dayText, 'daily') !== false || strpos($dayText, 'every') !== false;
                    ?>
                    <tr data-time-bucket="<?php echo e($timeBucket); ?>" data-is-weekday="<?php echo $isWeekday ? '1' : '0'; ?>" data-is-weekend="<?php echo $isWeekend ? '1' : '0'; ?>">
                        <td><a href="<?php echo e(url('shows/' . $row['slug'])); ?>" data-pjax><?php echo e($row['title']); ?></a></td>
                        <td><?php echo e($row['day_of_week']); ?></td>
                        <td><span class="show-time"><?php echo e(substr($row['start_time'], 0, 5)); ?> - <?php echo e(substr($row['end_time'], 0, 5)); ?></span></td>
                        <td><?php echo e($row['presenter']); ?></td>
                        <td>
                            <?php if (!empty($row['tones'])): ?>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($row['tones'] as $tone): ?>
                                        <span class="tone-badge"><?php echo e($tone); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div id="scheduleNoMatch" class="text-center text-muted py-3 d-none">No programs match the selected filters.</div>
        </div>
    </section>
</main>

<script>
(function () {
    var chips = document.querySelectorAll('.filter-chip[data-filter-group]');
    var rows = document.querySelectorAll('.schedule-table tbody tr');
    var clearBtn = document.getElementById('scheduleClearFilters');
    var noMatch = document.getElementById('scheduleNoMatch');
    var active = { time: '', day: '' };

    function render() {
        var visible = 0;

        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            var timeOk = !active.time || row.getAttribute('data-time-bucket') === active.time;
            var dayOk = true;

            if (active.day === 'weekdays') {
                dayOk = row.getAttribute('data-is-weekday') === '1';
            }
            if (active.day === 'weekend') {
                dayOk = row.getAttribute('data-is-weekend') === '1';
            }

            var show = timeOk && dayOk;
            row.classList.toggle('d-none', !show);
            if (show) {
                visible++;
            }
        }

        if (noMatch) {
            noMatch.classList.toggle('d-none', visible > 0);
        }
    }

    for (var i = 0; i < chips.length; i++) {
        chips[i].addEventListener('click', function () {
            var group = this.getAttribute('data-filter-group');
            var value = this.getAttribute('data-filter-value');

            if (!group || !value) {
                return;
            }

            if (active[group] === value) {
                active[group] = '';
                this.classList.remove('is-active');
            } else {
                active[group] = value;

                for (var j = 0; j < chips.length; j++) {
                    if (chips[j].getAttribute('data-filter-group') === group) {
                        chips[j].classList.remove('is-active');
                    }
                }

                this.classList.add('is-active');
            }

            render();
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            active.time = '';
            active.day = '';
            for (var i = 0; i < chips.length; i++) {
                chips[i].classList.remove('is-active');
            }
            render();
        });
    }
})();
</script>
