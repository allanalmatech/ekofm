<?php
$metaTitle = 'Contact - ' . APP_NAME;
$contactMapEmbed = trim(setting('contact_map_embed', ''));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf(isset($_POST['_token']) ? $_POST['_token'] : '')) {
        flash('error', 'Invalid request token.');
        redirect('contact');
    }

    $name = trim(isset($_POST['name']) ? $_POST['name'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $subject = trim(isset($_POST['subject']) ? $_POST['subject'] : '');
    $message = trim(isset($_POST['message']) ? $_POST['message'] : '');

    if ($name && $email && $message) {
        db_query('INSERT INTO contact_messages (name, email, subject, message, created_at, status) VALUES (?, ?, ?, ?, NOW(), ?)', array($name, $email, $subject, $message, 'new'));
        flash('success', 'Message sent successfully.');
    } else {
        flash('error', 'Please fill required fields.');
    }
    redirect('contact');
}
?>

<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-5 reveal">
            <div class="section-card floating-card h-100 story-section">
                <h2 class="mb-2">Contact EKO FM</h2>
                <p class="fw-semibold mb-1">Let's work together</p>
                <p class="mb-3"><strong>For Peace & Development</strong></p>

                <p class="mb-2"><strong>Address:</strong><br><?php echo e(setting('contact_address', 'Abim Road, Lokore Cells, Near Boma Grounds')); ?><br><?php echo e(setting('contact_location', 'Kotido, Karamoja, Uganda')); ?></p>
                <p class="mb-2"><strong>Phone:</strong> <?php echo e(setting('contact_phone', '+256 751 161 355')); ?></p>
                <p class="mb-2"><strong>Email:</strong> <?php echo e(setting('contact_email', 'info@eko.fm')); ?></p>
                <p class="mb-3"><strong>Station WhatsApp:</strong> <?php echo e(setting('contact_whatsapp', '0791 996450')); ?></p>

                <a class="btn btn-live btn-sm mb-3" href="<?php echo e(whatsapp_link(setting('contact_whatsapp', '0791996450'))); ?>" target="_blank">Chat on WhatsApp</a>
                <?php if ($contactMapEmbed !== ''): ?>
                    <div class="ratio ratio-4x3 rounded-3 overflow-hidden border">
                        <?php echo $contactMapEmbed; ?>
                    </div>
                <?php else: ?>
                    <div class="map-placeholder">Set map iframe in Admin Settings -> Contact tab.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-7 reveal reveal-delay-1">
            <div class="section-card floating-card story-section">
                <?php $okMsg = flash('success'); if ($okMsg): ?><div class="alert alert-success"><?php echo e($okMsg); ?></div><?php endif; ?>
                <?php $errMsg = flash('error'); if ($errMsg): ?><div class="alert alert-danger"><?php echo e($errMsg); ?></div><?php endif; ?>

                <form method="post">
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                    <div class="row g-3">
                        <div class="col-md-6"><input name="name" class="form-control" placeholder="Full name" required></div>
                        <div class="col-md-6"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
                        <div class="col-12"><input name="subject" class="form-control" placeholder="Subject"></div>
                        <div class="col-12"><textarea name="message" class="form-control" rows="6" placeholder="Message" required></textarea></div>
                        <div class="col-12"><button class="btn btn-live" type="submit">Send Message</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
