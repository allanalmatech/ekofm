<?php
$show = show_by_slug($slug);
if (!$show) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    return;
}

$metaTitle = $show['title'] . ' - EKO FM Shows';
$focusX = (int) (isset($show['cover_focus_x']) ? $show['cover_focus_x'] : 50);
$focusY = (int) (isset($show['cover_focus_y']) ? $show['cover_focus_y'] : 50);
$fullDescription = trim((string) (isset($show['full_description']) && $show['full_description'] !== '' ? $show['full_description'] : $show['description']));
$whatToExpect = trim((string) (isset($show['what_to_expect']) ? $show['what_to_expect'] : ''));

function day_index_by_name($name)
{
    $map = array(
        'monday' => 1,
        'tuesday' => 2,
        'wednesday' => 3,
        'thursday' => 4,
        'friday' => 5,
        'saturday' => 6,
        'sunday' => 7,
    );
    $key = strtolower(trim($name));
    return isset($map[$key]) ? $map[$key] : null;
}

function parse_show_days($dayOfWeek)
{
    $text = strtolower(trim((string) $dayOfWeek));
    if ($text === '' || $text === 'daily' || $text === 'everyday' || $text === 'every day') {
        return array(1, 2, 3, 4, 5, 6, 7);
    }

    if (strpos($text, 'monday') !== false && strpos($text, 'friday') !== false) {
        return array(1, 2, 3, 4, 5);
    }

    $days = array();
    foreach (array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') as $dayName) {
        if (strpos($text, $dayName) !== false) {
            $days[] = day_index_by_name($dayName);
        }
    }

    if (count($days) === 0) {
        return array(1, 2, 3, 4, 5, 6, 7);
    }

    return array_values(array_unique($days));
}

function minutes_from_time($time)
{
    $parts = explode(':', (string) $time);
    $hour = isset($parts[0]) ? (int) $parts[0] : 0;
    $minute = isset($parts[1]) ? (int) $parts[1] : 0;
    return ($hour * 60) + $minute;
}

function show_is_live($programRow, $currentDayIndex, $currentMinutes)
{
    $days = parse_show_days(isset($programRow['day_of_week']) ? $programRow['day_of_week'] : '');
    $start = minutes_from_time(isset($programRow['start_time']) ? $programRow['start_time'] : '00:00:00');
    $end = minutes_from_time(isset($programRow['end_time']) ? $programRow['end_time'] : '00:00:00');

    if ($start === $end) {
        return false;
    }

    if ($end > $start) {
        return in_array($currentDayIndex, $days, true) && $currentMinutes >= $start && $currentMinutes < $end;
    }

    $prevDay = $currentDayIndex === 1 ? 7 : ($currentDayIndex - 1);
    return (in_array($currentDayIndex, $days, true) && $currentMinutes >= $start)
        || (in_array($prevDay, $days, true) && $currentMinutes < $end);
}

function tone_class_name($tone)
{
    $key = strtolower((string) $tone);
    $key = preg_replace('/[^a-z0-9]+/', '-', $key);
    return trim((string) $key, '-');
}

$now = time();
$currentDayIndex = (int) date('N', $now);
$currentMinutes = ((int) date('G', $now) * 60) + (int) date('i', $now);
$isCurrentShowLive = show_is_live($show, $currentDayIndex, $currentMinutes);

$allShows = programs_list(true);
$nowPlaying = null;
foreach ($allShows as $row) {
    if (show_is_live($row, $currentDayIndex, $currentMinutes)) {
        $nowPlaying = $row;
        break;
    }
}

$nextShow = null;
$nextTimestamp = null;
$todayStart = strtotime(date('Y-m-d 00:00:00', $now));

foreach ($allShows as $row) {
    $days = parse_show_days($row['day_of_week']);
    $startMinutes = minutes_from_time($row['start_time']);

    foreach ($days as $dayIndex) {
        $offsetDays = ($dayIndex - $currentDayIndex + 7) % 7;
        $candidate = $todayStart + ($offsetDays * 86400) + ($startMinutes * 60);
        if ($candidate <= $now) {
            $candidate += 7 * 86400;
        }

        if ($nextTimestamp === null || $candidate < $nextTimestamp) {
            $nextTimestamp = $candidate;
            $nextShow = $row;
        }
    }
}

$language = ($show['slug'] === 'etem-a-karamoja') ? 'Ngakarimojong + English' : 'English';
?>
<main class="container py-4 show-detail-page">
    <section class="hero show-detail-hero mb-4 reveal story-section" data-parallax="0.2" data-focus-x="<?php echo e($focusX); ?>" data-focus-y="<?php echo e($focusY); ?>" style="min-height:480px;background-image:url('<?php echo e(media_url($show['cover_image'])); ?>');background-position:<?php echo e($focusX); ?>% <?php echo e($focusY); ?>%;">
        <div class="hero-content hero-card show-detail-hero-card">
            <a class="btn btn-glass btn-sm mb-3" href="<?php echo e(url('shows')); ?>" data-pjax>Back to Shows</a>
            <h1 class="mb-2"><?php echo e($show['title']); ?></h1>
            <p class="show-time mb-2"><?php echo e($show['day_of_week']); ?> | <?php echo e(substr($show['start_time'], 0, 5)); ?>-<?php echo e(substr($show['end_time'], 0, 5)); ?> <span class="live-badge <?php echo $isCurrentShowLive ? 'is-live' : ''; ?>"><?php echo $isCurrentShowLive ? '● LIVE' : '● OFF AIR'; ?></span></p>
            <?php if (!empty($show['tones'])): ?>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php foreach ($show['tones'] as $tone): ?>
                        <span class="tone-badge tone-<?php echo e(tone_class_name($tone)); ?>"><?php echo e($tone); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
         
        </div>
    </section>

    <section class="section-card floating-card reveal story-section show-detail-about">
        <div class="about-grid">
            <div>
                <h4 class="mb-3">About This Show</h4>
                <?php if (!empty($show['tones'])): ?>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <?php foreach ($show['tones'] as $tone): ?>
                            <span class="tone-badge tone-<?php echo e(tone_class_name($tone)); ?>"><?php echo e($tone); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <p class="text-muted"><?php echo e($fullDescription); ?></p>

                <?php if ($whatToExpect !== ''): ?>
                    <div class="mt-4">
                        <h5 class="mb-2">What to Expect</h5>
                        <p class="text-muted mb-0"><?php echo nl2br(e($whatToExpect)); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="show-meta-card">
                <h6 class="mb-3">Show Info</h6>
                <p><strong>Host:</strong> <?php echo e($show['presenter']); ?></p>
                <p><strong>Language:</strong> <?php echo e($language); ?></p>
                <p><strong>Schedule:</strong> <?php echo e($show['day_of_week']); ?> | <?php echo e(substr($show['start_time'], 0, 5)); ?>-<?php echo e(substr($show['end_time'], 0, 5)); ?></p>
                <p class="mb-2"><strong>Status:</strong> <?php echo $isCurrentShowLive ? 'Live now' : 'Off air'; ?></p>
                <?php if ($nowPlaying): ?>
                    <p class="mb-1"><strong>Now Playing:</strong> <?php echo e($nowPlaying['title']); ?></p>
                <?php endif; ?>
                <?php if ($nextShow): ?>
                    <p class="mb-0"><strong>Next Show:</strong> <?php echo e($nextShow['title']); ?> (<?php echo e(substr($nextShow['start_time'], 0, 5)); ?>)</p>
                <?php endif; ?>
            </aside>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <a class="btn show-detail-btn-primary" href="<?php echo e(url('listen-live')); ?>" data-pjax><span class="material-symbols-outlined">play_arrow</span> Listen Live</a>
            <a class="btn show-detail-btn-secondary" href="<?php echo e(url('advertise-partner')); ?>" data-pjax>Sponsor This Show</a>
        </div>
    </section>
</main>
