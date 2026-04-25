<?php
require_once __DIR__ . '/includes/db.php';

function out($text)
{
    echo $text . PHP_EOL;
}

function now()
{
    return date('Y-m-d H:i:s');
}

function upsert_page($slug, $title, $content, $metaTitle = '', $metaDescription = '', $status = 1)
{
    $existing = db_query('SELECT id FROM pages WHERE slug = ? LIMIT 1', array($slug))->fetch();
    if ($existing) {
        db_query(
            'UPDATE pages SET title=?, content=?, meta_title=?, meta_description=?, status=?, updated_at=? WHERE id=?',
            array($title, $content, $metaTitle, $metaDescription, (int) $status, now(), (int) $existing['id'])
        );
        return (int) $existing['id'];
    }

    db_query(
        'INSERT INTO pages (title, slug, content, meta_title, meta_description, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
        array($title, $slug, $content, $metaTitle, $metaDescription, (int) $status, now(), now())
    );
    return (int) db()->lastInsertId();
}

function upsert_page_section($pageId, $section)
{
    $existing = db_query(
        'SELECT id FROM page_sections WHERE page_id = ? AND section_key = ? ORDER BY id ASC LIMIT 1',
        array((int) $pageId, $section['section_key'])
    )->fetch();

    $params = array(
        (int) $pageId,
        $section['section_key'],
        $section['title'],
        $section['content'],
        isset($section['cta_text']) ? $section['cta_text'] : '',
        isset($section['cta_link']) ? $section['cta_link'] : '',
        isset($section['image_path']) ? $section['image_path'] : null,
        isset($section['sort_order']) ? (int) $section['sort_order'] : 1,
        isset($section['is_visible']) ? (int) $section['is_visible'] : 1,
    );

    if ($existing) {
        $params[] = now();
        $params[] = (int) $existing['id'];
        db_query(
            'UPDATE page_sections SET page_id=?, section_key=?, title=?, content=?, cta_text=?, cta_link=?, image_path=?, sort_order=?, is_visible=?, updated_at=? WHERE id=?',
            $params
        );
        return;
    }

    $params[] = now();
    $params[] = now();
    db_query(
        'INSERT INTO page_sections (page_id, section_key, title, content, cta_text, cta_link, image_path, sort_order, is_visible, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        $params
    );
}

function upsert_home_section($key, $title, $sort, $status)
{
    db_query(
        'INSERT INTO homepage_sections (section_key, section_title, sort_order, status, updated_at) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE section_title=VALUES(section_title), sort_order=VALUES(sort_order), status=VALUES(status), updated_at=VALUES(updated_at)',
        array($key, $title, (int) $sort, (int) $status, now())
    );
}

function upsert_program($program)
{
    $existing = db_query('SELECT id FROM programs WHERE slug = ? ORDER BY id ASC LIMIT 1', array($program['slug']))->fetch();

    $params = array(
        $program['title'],
        $program['slug'],
        $program['presenter'],
        isset($program['cover_image']) ? $program['cover_image'] : '',
        isset($program['cover_focus_x']) ? (int) $program['cover_focus_x'] : 50,
        isset($program['cover_focus_y']) ? (int) $program['cover_focus_y'] : 50,
        $program['day_of_week'],
        $program['start_time'],
        $program['end_time'],
        $program['description'],
        isset($program['status']) ? (int) $program['status'] : 1,
    );

    if ($existing) {
        $params[] = now();
        $params[] = (int) $existing['id'];
        db_query(
            'UPDATE programs SET title=?, slug=?, presenter=?, cover_image=?, cover_focus_x=?, cover_focus_y=?, day_of_week=?, start_time=?, end_time=?, description=?, status=?, updated_at=? WHERE id=?',
            $params
        );
        return;
    }

    $params[] = now();
    $params[] = now();
    db_query(
        'INSERT INTO programs (title, slug, presenter, cover_image, cover_focus_x, cover_focus_y, day_of_week, start_time, end_time, description, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        $params
    );
}

function ensure_program_tones_tables()
{
    db_query('CREATE TABLE IF NOT EXISTS tones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NOT NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');

    db_query('CREATE TABLE IF NOT EXISTS program_tones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        program_id INT,
        tone_id INT,
        UNIQUE KEY uniq_program_tone (program_id, tone_id),
        KEY idx_program_tones_program (program_id),
        KEY idx_program_tones_tone (tone_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci');
}

function sync_program_tones()
{
    $toneNames = array(
        'Authoritative',
        'Engaging',
        'Community-driven',
        'Energetic',
        'Smart',
        'Interactive',
        'Honest',
        'Cultural',
        'Bold',
        'Community-first',
        'Youthful',
        'High-impact',
        'Entertainment-driven',
        'Warm',
        'Nostalgic',
        'Story-driven',
        'Relaxed',
        'Spiritual',
        'Inspirational',
        'Uplifting',
        'Fun',
        'Educational',
        'Colorful',
        'Practical',
        'Friendly',
        'Informative',
    );

    foreach ($toneNames as $toneName) {
        db_query('INSERT INTO tones (name) VALUES (?) ON DUPLICATE KEY UPDATE name=VALUES(name)', array($toneName));
    }

    $toneRows = db_query('SELECT id, name FROM tones')->fetchAll();
    $toneIdByName = array();
    foreach ($toneRows as $toneRow) {
        $toneIdByName[$toneRow['name']] = (int) $toneRow['id'];
    }

    $toneAssignments = array(
        array(
            'slugs' => array('the-news-roundup', 'the-news-round-up'),
            'tones' => array('Authoritative', 'Engaging', 'Community-driven'),
        ),
        array(
            'slugs' => array('the-sports-digest'),
            'tones' => array('Energetic', 'Smart', 'Interactive', 'Authoritative'),
        ),
        array(
            'slugs' => array('etem-a-karamoja'),
            'tones' => array('Honest', 'Cultural', 'Bold', 'Community-first'),
        ),
        array(
            'slugs' => array('eko-top-20', 'the-eko-top-20'),
            'tones' => array('Energetic', 'Youthful', 'High-impact', 'Entertainment-driven'),
        ),
        array(
            'slugs' => array('eko-classics-oldies', 'eko-classics'),
            'tones' => array('Warm', 'Nostalgic', 'Story-driven', 'Relaxed'),
        ),
        array(
            'slugs' => array('gospel-explosion', 'the-gospel-explosion'),
            'tones' => array('Spiritual', 'Inspirational', 'Uplifting'),
        ),
        array(
            'slugs' => array('eko-kids'),
            'tones' => array('Fun', 'Educational', 'Interactive', 'Colorful'),
        ),
        array(
            'slugs' => array('healthy-living'),
            'tones' => array('Practical', 'Friendly', 'Informative'),
        ),
    );

    db_query('DELETE FROM program_tones');

    foreach ($toneAssignments as $assignment) {
        $slugs = isset($assignment['slugs']) ? $assignment['slugs'] : array();
        if (count($slugs) === 0) {
            continue;
        }

        $placeholders = implode(',', array_fill(0, count($slugs), '?'));
        $programRows = db_query('SELECT id FROM programs WHERE slug IN (' . $placeholders . ')', $slugs)->fetchAll();

        foreach ($programRows as $programRow) {
            $programId = (int) $programRow['id'];
            foreach ($assignment['tones'] as $toneName) {
                if (!isset($toneIdByName[$toneName])) {
                    continue;
                }
                db_query(
                    'INSERT INTO program_tones (program_id, tone_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE tone_id=VALUES(tone_id)',
                    array($programId, $toneIdByName[$toneName])
                );
            }
        }
    }
}

try {
    $mediaKitEmail = 'partnerships@ekoradio.fm';
    $mediaKitLink = 'mailto:' . $mediaKitEmail . '?subject=Sponsorship%20Media%20Kit%20Request';

    ensure_program_tones_tables();
    db()->beginTransaction();

    upsert_home_section('activations', 'Live & Community Activations', 6, 1);

    $activationsPageId = upsert_page(
        'activations',
        'Live & Community Activations',
        'EKO FM goes beyond the airwaves to connect with communities in real life. Through our Live & Community Activations, we create meaningful experiences that bring people together, drive impact, and give brands powerful ways to engage directly with their audience.',
        'Live & Community Activations - EKO FM',
        'From Radio to Real Life: community impact initiatives and live experiences by EKO FM.',
        1
    );

    $activationSections = array(
        array(
            'section_key' => 'hero',
            'title' => 'LIVE & COMMUNITY ACTIVATIONS',
            'content' => 'From community outreach and health initiatives to high-energy events and entertainment experiences, EKO FM is where radio meets real life.',
            'cta_text' => 'Download Sponsorship Kit',
            'cta_link' => $mediaKitLink,
            'sort_order' => 1,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'intro',
            'title' => 'Page Intro',
            'content' => 'EKO FM creates activations that bring listeners, partners, and communities together through practical outreach and memorable live moments.',
            'sort_order' => 2,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'community_eko_clean_kotido',
            'title' => 'EKO CLEAN KOTIDO',
            'content' => 'A hands-on initiative focused on cleanliness, hygiene, and environmental responsibility through organized clean-up drives and awareness campaigns.',
            'sort_order' => 3,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'community_eko_fm_cares',
            'title' => 'EKO FM CARES',
            'content' => 'A heartfelt initiative supporting communities in times of need through hospital visits and outreach programs that spread care and hope.',
            'sort_order' => 4,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'community_eko_health_camp',
            'title' => 'EKO HEALTH CAMP',
            'content' => 'A community health initiative providing free medical check-ups, health education, and awareness services in partnership with professionals.',
            'sort_order' => 5,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'community_heart_of_christmas',
            'title' => 'EKO FM HEART OF CHRISTMAS',
            'content' => 'A festive outreach initiative supporting families during the holiday season through donations, celebrations, and shared experiences.',
            'sort_order' => 6,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'live_bicycle_race',
            'title' => 'EKO FM BICYCLE RACE',
            'content' => 'An annual high-energy event bringing together riders, fans, and brands in a celebration of fitness, youth engagement, and community spirit.',
            'sort_order' => 7,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'live_eko_fm_night',
            'title' => 'THE EKO FM NIGHT',
            'content' => 'A nightlife experience where the sound of EKO FM comes alive with electrifying DJ sets, unforgettable crowd moments, and nonstop energy.',
            'sort_order' => 8,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'sponsor_cta',
            'title' => 'PARTNER WITH US',
            'content' => 'EKO FM offers flexible sponsorship opportunities across all activations, giving brands direct access to engaged audiences on-air and on-ground.',
            'cta_text' => 'Download Media Kit',
            'cta_link' => $mediaKitLink,
            'sort_order' => 9,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'footer_cta',
            'title' => 'Want to partner with us? Contact us today.',
            'content' => 'For now, request the sponsorship media kit directly via email at partnerships@ekoradio.fm.',
            'cta_text' => 'Email partnerships@ekoradio.fm',
            'cta_link' => $mediaKitLink,
            'sort_order' => 10,
            'is_visible' => 1,
        ),
    );

    foreach ($activationSections as $section) {
        upsert_page_section($activationsPageId, $section);
    }

    $teamPageId = upsert_page(
        'our-team',
        'Meet the People Behind EKO FM',
        'From the voices you hear on-air to the minds shaping the sound, EKO FM is powered by a passionate team dedicated to quality radio and community impact.',
        'Our Team - EKO FM',
        'Management, advisor, and on-air personalities behind EKO FM.',
        1
    );

    $teamSections = array(
        array(
            'section_key' => 'management_adingdong_stephen_okech',
            'title' => 'ADINGDONG STEPHEN OKECH',
            'cta_text' => 'Station Manager - EKO FM',
            'content' => 'Experienced, strategic, and community-focused. He leads overall vision and operations, ensuring relevant, high-quality content for listeners.',
            'sort_order' => 1,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'management_apolot_lydia',
            'title' => 'APOLOT LYDIA',
            'cta_text' => 'Programs Director - EKO FM',
            'content' => 'Bold, creative, and audience-focused. She shapes station sound, content flow, and show structure with strong industry insight.',
            'sort_order' => 2,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'management_jennifer_aceng',
            'title' => 'JENNIFER ACENG',
            'cta_text' => 'Head of News - EKO FM',
            'content' => 'Sharp, credible, and detail-oriented. She leads newsroom editorial direction for accurate, timely, and impactful coverage.',
            'sort_order' => 3,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'management_enwou_mark',
            'title' => 'ENWOU MARK',
            'cta_text' => 'Technical Director - EKO FM',
            'content' => 'Skilled, precise, and reliability-focused. He oversees technical operations to ensure smooth broadcasting and quality sound.',
            'sort_order' => 4,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'management_osilo_michael',
            'title' => 'OSILO MICHAEL (KEMMY CALI)',
            'cta_text' => 'Productions Manager - EKO FM | Head of Production - EKO Records',
            'content' => 'Creative and sound-driven. He leads production direction and audio identity, while driving music production and talent development.',
            'sort_order' => 5,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'advisor_james_peterson',
            'title' => 'JAMES PETERSON',
            'cta_text' => 'Programming, Branding & Marketing, Technical/Broadcast Consultant',
            'content' => 'Strategic and detail-driven advisor shaping programming structure, brand identity, and technical sound for consistent excellence.',
            'sort_order' => 6,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'personality_titus',
            'title' => 'TITUS',
            'cta_text' => 'Morning Show Host - Maata Karamoja',
            'content' => 'A strong and engaging morning voice bringing energy, awareness, and community connection across Karamoja.',
            'sort_order' => 7,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'personality_sharon',
            'title' => 'SHARON',
            'cta_text' => 'Co-Host - Maata Karamoja',
            'content' => 'Warm, vibrant, and relatable. She connects deeply with listeners through storytelling, empathy, and culture.',
            'sort_order' => 8,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'personality_auntie_ruth',
            'title' => 'AUNTIE RUTH',
            'cta_text' => 'Host - Straight Talk',
            'content' => 'Fearless and honest. Known for tough love and direct conversations that challenge listeners to grow.',
            'sort_order' => 9,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'personality_jennifer',
            'title' => 'JENNIFER',
            'cta_text' => 'Host - Request Lunch Hour',
            'content' => 'Fun, lively, and interactive. She keeps energy high through requests, shoutouts, and dedications.',
            'sort_order' => 10,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'personality_capital_p',
            'title' => 'CAPITAL P',
            'cta_text' => 'Host - Daily Sports Updates & The Sports Digest',
            'content' => 'A passionate sports analyst delivering insightful breakdowns, interviews, and updates for fans.',
            'sort_order' => 11,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'personality_lydia',
            'title' => 'LYDIA',
            'cta_text' => 'Host - EKO Drive',
            'content' => 'Bold and energetic. She drives conversations that matter with relatable and youth-focused topics.',
            'sort_order' => 12,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'personality_lomojomoi',
            'title' => 'LOMOJOMOI',
            'cta_text' => 'Host - The EKO Situation',
            'content' => 'Sharp, fearless, and uncompromising. He leads accountability-driven conversations focused on truth and impact.',
            'sort_order' => 13,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'personality_uncle_sam',
            'title' => 'UNCLE SAM',
            'cta_text' => 'Host - EKO Kids',
            'content' => 'Fun and lively. He brings joy to young listeners through music, storytelling, and learning moments.',
            'sort_order' => 14,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'personality_angel',
            'title' => 'ANGEL',
            'cta_text' => 'Host - EKO After Dark',
            'content' => 'Calm and empathetic. She creates a safe late-night space for reflection and emotional connection.',
            'sort_order' => 15,
            'is_visible' => 1,
        ),
        array(
            'section_key' => 'personality_dj_marxis',
            'title' => 'DJ MARXIS',
            'cta_text' => 'Host - EKO Live Wire',
            'content' => 'High-energy and electrifying. He powers EKO FM nightlife with seamless mixes and nonstop party vibes.',
            'sort_order' => 16,
            'is_visible' => 1,
        ),
    );

    foreach ($teamSections as $section) {
        upsert_page_section($teamPageId, $section);
    }

    $programs = array(
        array(
            'title' => 'Maata Karamoja',
            'slug' => 'maata-karamoja',
            'presenter' => 'Titus & Sharon',
            'day_of_week' => 'Monday - Friday',
            'start_time' => '06:30:00',
            'end_time' => '10:00:00',
            'description' => 'Start your day with the heartbeat of Karamoja through news, culture, community voices, and interactive conversations.',
            'status' => 1,
        ),
        array(
            'title' => 'Straight Talk',
            'slug' => 'straight-talk',
            'presenter' => 'Auntie Ruth',
            'day_of_week' => 'Monday - Friday',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'description' => 'A bold no-nonsense show tackling relationships, health, discipline, and life choices with practical and honest advice.',
            'status' => 1,
        ),
        array(
            'title' => 'Midday Express',
            'slug' => 'midday-express',
            'presenter' => 'EKO FM Team',
            'day_of_week' => 'Monday - Friday',
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
            'description' => 'A non-stop high-energy music mix designed to recharge your day during lunch break and midday hours.',
            'status' => 1,
        ),
        array(
            'title' => 'EKO Request Lunch Hour',
            'slug' => 'eko-request-lunch-hour',
            'presenter' => 'Jennifer',
            'day_of_week' => 'Monday - Friday',
            'start_time' => '13:00:00',
            'end_time' => '15:00:00',
            'description' => 'An interactive request show driven by listener dedications, shoutouts, and live engagement.',
            'status' => 1,
        ),
        array(
            'title' => 'EKO Drive',
            'slug' => 'eko-drive',
            'presenter' => 'Lydia',
            'day_of_week' => 'Monday - Friday',
            'start_time' => '15:00:00',
            'end_time' => '19:00:00',
            'description' => 'Your afternoon companion with music, conversation, vox pops, and listener interaction on the journey home.',
            'status' => 1,
        ),
        array(
            'title' => 'The EKO Situation',
            'slug' => 'the-eko-situation',
            'presenter' => 'Lomojomoi',
            'day_of_week' => 'Monday - Friday',
            'start_time' => '19:00:00',
            'end_time' => '21:00:00',
            'description' => 'A serious talk-driven show focused on politics, governance, and community issues with informed dialogue.',
            'status' => 1,
        ),
        array(
            'title' => 'EKO After Dark',
            'slug' => 'eko-after-dark',
            'presenter' => 'Angel',
            'day_of_week' => 'Daily',
            'start_time' => '22:00:00',
            'end_time' => '01:00:00',
            'description' => 'A smooth late-night show exploring love, relationships, life struggles, and emotional listener stories.',
            'status' => 1,
        ),
        array(
            'title' => 'The News Roundup',
            'slug' => 'the-news-roundup',
            'presenter' => 'The News Junkies',
            'day_of_week' => 'Saturday',
            'start_time' => '06:00:00',
            'end_time' => '08:00:00',
            'description' => 'EKO FM flagship Saturday news show with local, national, and global stories explained through a community lens.',
            'status' => 1,
        ),
        array(
            'title' => 'The Sports Digest',
            'slug' => 'the-sports-digest',
            'presenter' => 'Capital P',
            'day_of_week' => 'Saturday',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'description' => 'Saturday sports breakdown with match analysis, local coverage, tactical insights, and listener participation.',
            'status' => 1,
        ),
        array(
            'title' => 'Etem A Karamoja',
            'slug' => 'etem-a-karamoja',
            'presenter' => 'EKO FM Team',
            'day_of_week' => 'Saturday',
            'start_time' => '10:00:00',
            'end_time' => '13:00:00',
            'description' => 'The voice of Karamoja: honest community-first conversations featuring local voices, experts, and call-ins.',
            'status' => 1,
        ),
        array(
            'title' => 'EKO Top 20',
            'slug' => 'eko-top-20',
            'presenter' => 'EKO FM Team',
            'day_of_week' => 'Saturday',
            'start_time' => '13:00:00',
            'end_time' => '15:00:00',
            'description' => 'The official weekend chart show counting down the hottest songs shaping the sound of Karamoja.',
            'status' => 1,
        ),
        array(
            'title' => 'EKO Classics / EKO Oldies',
            'slug' => 'eko-classics-oldies',
            'presenter' => 'EKO FM Team',
            'day_of_week' => 'Saturday',
            'start_time' => '15:00:00',
            'end_time' => '19:00:00',
            'description' => 'A nostalgic and story-driven oldies journey filled with timeless songs and shared memories.',
            'status' => 1,
        ),
        array(
            'title' => 'African Express',
            'slug' => 'african-express',
            'presenter' => 'EKO FM Team',
            'day_of_week' => 'Saturday',
            'start_time' => '19:00:00',
            'end_time' => '20:00:00',
            'description' => 'A nonstop hour of classic African hits celebrating rhythm, heritage, and golden-era sounds.',
            'status' => 1,
        ),
        array(
            'title' => 'EKO Live Wire',
            'slug' => 'eko-live-wire',
            'presenter' => 'DJ MarxIs',
            'day_of_week' => 'Saturday',
            'start_time' => '20:00:00',
            'end_time' => '02:00:00',
            'description' => 'The ultimate Saturday night party mix with Afrobeat, Amapiano, dancehall, and club anthems.',
            'status' => 1,
        ),
        array(
            'title' => 'Gospel Explosion',
            'slug' => 'gospel-explosion',
            'presenter' => 'EKO FM Team',
            'day_of_week' => 'Sunday',
            'start_time' => '05:00:00',
            'end_time' => '10:00:00',
            'description' => 'A spiritual and uplifting Sunday show blending worship, testimonies, and encouragement.',
            'status' => 1,
        ),
        array(
            'title' => 'EKO Kids',
            'slug' => 'eko-kids',
            'presenter' => 'Uncle Sam',
            'day_of_week' => 'Sunday',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'description' => 'A fun, educational, and interactive space for young listeners through music, stories, and games.',
            'status' => 1,
        ),
        array(
            'title' => 'Healthy Living',
            'slug' => 'healthy-living',
            'presenter' => 'EKO FM Team',
            'day_of_week' => 'Sunday',
            'start_time' => '12:00:00',
            'end_time' => '14:00:00',
            'description' => 'Friendly and practical health education on nutrition, hygiene, and everyday wellbeing.',
            'status' => 1,
        ),
        array(
            'title' => 'EKO Country',
            'slug' => 'eko-country',
            'presenter' => 'EKO FM Team',
            'day_of_week' => 'Sunday',
            'start_time' => '14:00:00',
            'end_time' => '17:00:00',
            'description' => 'A relaxing edition of heartfelt country music, storytelling, and listener dedications.',
            'status' => 1,
        ),
        array(
            'title' => 'Reggae Sunset',
            'slug' => 'reggae-sunset',
            'presenter' => 'EKO FM Team',
            'day_of_week' => 'Sunday',
            'start_time' => '17:00:00',
            'end_time' => '19:00:00',
            'description' => 'A peaceful Sunday sunset session of roots reggae with conscious and reflective listening.',
            'status' => 1,
        ),
        array(
            'title' => 'The EKO Doctor',
            'slug' => 'the-eko-doctor',
            'presenter' => 'EKO FM Health Desk',
            'day_of_week' => 'Sunday',
            'start_time' => '19:00:00',
            'end_time' => '21:00:00',
            'description' => 'Your Health, Your Life, Your Community: a trusted health talk show focused on awareness and practical solutions.',
            'status' => 1,
        ),
    );

    foreach ($programs as $program) {
        upsert_program($program);
    }

    sync_program_tones();

    db()->commit();
    out('Seed update completed successfully.');
} catch (Exception $e) {
    if (db()->inTransaction()) {
        db()->rollBack();
    }
    out('Seed update failed: ' . $e->getMessage());
    exit(1);
}
