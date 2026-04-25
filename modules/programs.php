<?php
require_once __DIR__ . '/../includes/functions.php';

function programs_tones_enabled()
{
    static $enabled = null;
    if ($enabled !== null) {
        return $enabled;
    }

    $hasTones = db_query("SHOW TABLES LIKE 'tones'")->fetch();
    $hasProgramTones = db_query("SHOW TABLES LIKE 'program_tones'")->fetch();
    $enabled = (bool) ($hasTones && $hasProgramTones);
    return $enabled;
}

function attach_program_tones($rows)
{
    if (!is_array($rows) || count($rows) === 0 || !programs_tones_enabled()) {
        return $rows;
    }

    $ids = array();
    foreach ($rows as $row) {
        if (isset($row['id'])) {
            $ids[] = (int) $row['id'];
        }
    }

    if (count($ids) === 0) {
        return $rows;
    }

    $ids = array_values(array_unique($ids));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $toneRows = db_query(
        'SELECT pt.program_id, t.name FROM program_tones pt INNER JOIN tones t ON t.id = pt.tone_id WHERE pt.program_id IN (' . $placeholders . ') ORDER BY t.name ASC',
        $ids
    )->fetchAll();

    $map = array();
    foreach ($toneRows as $toneRow) {
        $programId = (int) $toneRow['program_id'];
        if (!isset($map[$programId])) {
            $map[$programId] = array();
        }
        $map[$programId][] = $toneRow['name'];
    }

    foreach ($rows as &$row) {
        $rowId = (int) $row['id'];
        $row['tones'] = isset($map[$rowId]) ? $map[$rowId] : array();
    }
    unset($row);

    return $rows;
}

function programs_list($onlyPublished)
{
    if ($onlyPublished) {
        $stmt = db_query('SELECT * FROM programs WHERE status = 1 ORDER BY day_of_week ASC, start_time ASC');
        return attach_program_tones($stmt->fetchAll());
    }
    $stmt = db_query('SELECT * FROM programs ORDER BY id DESC');
    return attach_program_tones($stmt->fetchAll());
}

function program_day_indexes($dayOfWeek)
{
    $text = strtolower(trim((string) $dayOfWeek));
    if ($text === '' || $text === 'daily' || $text === 'everyday' || $text === 'every day') {
        return array(1, 2, 3, 4, 5, 6, 7);
    }

    if (strpos($text, 'monday') !== false && strpos($text, 'friday') !== false) {
        return array(1, 2, 3, 4, 5);
    }

    $map = array(
        'monday' => 1,
        'tuesday' => 2,
        'wednesday' => 3,
        'thursday' => 4,
        'friday' => 5,
        'saturday' => 6,
        'sunday' => 7,
    );

    $days = array();
    foreach ($map as $name => $idx) {
        if (strpos($text, $name) !== false) {
            $days[] = $idx;
        }
    }

    if (!$days) {
        return array(1, 2, 3, 4, 5, 6, 7);
    }

    return array_values(array_unique($days));
}

function program_minutes_from_time($time)
{
    $parts = explode(':', (string) $time);
    $hour = isset($parts[0]) ? (int) $parts[0] : 0;
    $minute = isset($parts[1]) ? (int) $parts[1] : 0;
    return ($hour * 60) + $minute;
}

function program_is_live_at($programRow, $dayIndex, $minutes)
{
    $days = program_day_indexes(isset($programRow['day_of_week']) ? $programRow['day_of_week'] : '');
    $start = program_minutes_from_time(isset($programRow['start_time']) ? $programRow['start_time'] : '00:00:00');
    $end = program_minutes_from_time(isset($programRow['end_time']) ? $programRow['end_time'] : '00:00:00');

    if ($start === $end) {
        return false;
    }

    if ($end > $start) {
        return in_array((int) $dayIndex, $days, true) && $minutes >= $start && $minutes < $end;
    }

    $prevDay = (int) $dayIndex === 1 ? 7 : ((int) $dayIndex - 1);
    return (in_array((int) $dayIndex, $days, true) && $minutes >= $start)
        || (in_array($prevDay, $days, true) && $minutes < $end);
}

function current_program_on_air($timestamp = null)
{
    $ts = $timestamp !== null ? (int) $timestamp : time();
    $dayIndex = (int) date('N', $ts);
    $minutes = ((int) date('G', $ts) * 60) + (int) date('i', $ts);

    $rows = programs_list(true);
    foreach ($rows as $row) {
        if (program_is_live_at($row, $dayIndex, $minutes)) {
            return $row;
        }
    }

    return null;
}

function next_program_on_air($timestamp = null)
{
    $ts = $timestamp !== null ? (int) $timestamp : time();
    $currentDayIndex = (int) date('N', $ts);
    $todayStart = strtotime(date('Y-m-d 00:00:00', $ts));

    $rows = programs_list(true);
    $nextShow = null;
    $nextTimestamp = null;

    foreach ($rows as $row) {
        $days = program_day_indexes(isset($row['day_of_week']) ? $row['day_of_week'] : '');
        $startMinutes = program_minutes_from_time(isset($row['start_time']) ? $row['start_time'] : '00:00:00');

        foreach ($days as $dayIndex) {
            $offsetDays = ($dayIndex - $currentDayIndex + 7) % 7;
            $candidate = $todayStart + ($offsetDays * 86400) + ($startMinutes * 60);
            if ($candidate <= $ts) {
                $candidate += 7 * 86400;
            }

            if ($nextTimestamp === null || $candidate < $nextTimestamp) {
                $nextTimestamp = $candidate;
                $nextShow = $row;
            }
        }
    }

    if (!$nextShow) {
        return null;
    }

    $nextShow['next_start_timestamp'] = $nextTimestamp;
    return $nextShow;
}

function featured_shows($limit)
{
    $stmt = db_query('SELECT * FROM programs WHERE status = 1 ORDER BY id ASC LIMIT ' . (int) $limit);
    return attach_program_tones($stmt->fetchAll());
}

function show_by_slug($slug)
{
    $stmt = db_query('SELECT * FROM programs WHERE slug = ? AND status = 1 LIMIT 1', array($slug));
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }

    $rows = attach_program_tones(array($row));
    return $rows ? $rows[0] : $row;
}
