<?php
$metaTitle = 'Our Team - EKO FM';
$metaDescription = 'Meet the management team, advisor, and on-air personalities behind EKO FM.';

$teamPage = get_page_by_slug('our-team');
$sections = $teamPage ? get_page_sections((int) $teamPage['id']) : array();

$management = array();
$advisor = array();
$personalities = array();

foreach ($sections as $section) {
    if ((int) $section['is_visible'] !== 1) {
        continue;
    }
    if (strpos($section['section_key'], 'management_') === 0) {
        $management[] = $section;
    } elseif (strpos($section['section_key'], 'advisor_') === 0) {
        $advisor[] = $section;
    } elseif (strpos($section['section_key'], 'personality_') === 0) {
        $personalities[] = $section;
    }
}
?>

<main class="container py-4">
    <section class="section-card floating-card mb-4 reveal story-section">
        <h1 class="mb-2"><?php echo e($teamPage ? $teamPage['title'] : 'Our Team'); ?></h1>
        <p class="text-muted mb-0"><?php echo e($teamPage ? $teamPage['content'] : 'Meet the people behind EKO FM.'); ?></p>
    </section>

    <section class="section-space story-section">
        <h3 class="section-title reveal">Management Team</h3>
        <div class="row g-4">
            <?php foreach ($management as $index => $member): ?>
                <div class="col-md-6 col-lg-4 reveal reveal-delay-<?php echo e(($index % 3) + 1); ?>">
                    <article class="section-card floating-card team-card h-100">
                        <div class="team-image-slot mb-3" style="background-image:url('<?php echo e(!empty($member['image_path']) ? media_url($member['image_path']) : media_url('')); ?>');">
                            <?php if (empty($member['image_path'])): ?><span>Photo</span><?php endif; ?>
                        </div>
                        <h5 class="mb-1"><?php echo e($member['title']); ?></h5>
                        <?php if (!empty($member['cta_text'])): ?><p class="small text-uppercase text-muted mb-2" style="letter-spacing:.08em;"><?php echo e($member['cta_text']); ?></p><?php endif; ?>
                        <p class="text-muted mb-0"><?php echo e($member['content']); ?></p>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section-card floating-card mb-4 reveal reveal-delay-1 story-section">
        <h3 class="mb-3">Consultant & Advisor</h3>
        <div class="row g-3">
            <?php foreach ($advisor as $member): ?>
                <div class="col-lg-8">
                    <article class="d-flex gap-3 align-items-start">
                        <div class="team-image-slot team-image-slot-sm" style="background-image:url('<?php echo e(!empty($member['image_path']) ? media_url($member['image_path']) : media_url('')); ?>');">
                            <?php if (empty($member['image_path'])): ?><span>Photo</span><?php endif; ?>
                        </div>
                        <div>
                            <h5 class="mb-1"><?php echo e($member['title']); ?></h5>
                            <?php if (!empty($member['cta_text'])): ?><p class="small text-uppercase text-muted mb-2" style="letter-spacing:.08em;"><?php echo e($member['cta_text']); ?></p><?php endif; ?>
                            <p class="text-muted mb-0"><?php echo e($member['content']); ?></p>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section-space story-section">
        <h3 class="section-title reveal">On-Air Personalities</h3>
        <div class="row g-4">
            <?php foreach ($personalities as $index => $member): ?>
                <div class="col-md-6 col-lg-4 reveal reveal-delay-<?php echo e(($index % 3) + 1); ?>">
                    <article class="section-card floating-card team-card h-100">
                        <div class="team-image-slot mb-3" style="background-image:url('<?php echo e(!empty($member['image_path']) ? media_url($member['image_path']) : media_url('')); ?>');">
                            <?php if (empty($member['image_path'])): ?><span>Photo</span><?php endif; ?>
                        </div>
                        <h5 class="mb-1"><?php echo e($member['title']); ?></h5>
                        <?php if (!empty($member['cta_text'])): ?><p class="small text-uppercase text-muted mb-2" style="letter-spacing:.08em;"><?php echo e($member['cta_text']); ?></p><?php endif; ?>
                        <p class="text-muted mb-0"><?php echo e($member['content']); ?></p>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
