    </div>
    <footer class="site-footer">
        <div class="container-xxl py-5">
            <div class="row g-4 align-items-start">
                <div class="col-lg-4">
                    <h5 class="mb-1">EKO FM</h5>
                    <p class="mb-1"><?php echo e(setting('site_tagline', 'The Heartbeat of Karamoja')); ?></p>
                    <p class="mb-3"><?php echo e(setting('footer_tagline', 'On-Air. Online. On-Ground. For Peace & Development')); ?></p>
                    <a class="btn btn-live btn-sm" href="<?php echo e(url('listen-live')); ?>" data-pjax>Listen Live</a>
                </div>

                <div class="col-lg-4">
                    <h6 class="mb-3">Contact</h6>
                    <p class="mb-1"><?php echo e(setting('contact_address', 'Abim Road, Lokore Cells, Near Boma Grounds')); ?></p>
                    <p class="mb-1"><?php echo e(setting('contact_location', 'Kotido, Karamoja, Uganda')); ?></p>
                    <p class="mb-1"><?php echo e(setting('contact_phone', '+256 751 161 355')); ?></p>
                    <p class="mb-3"><?php echo e(setting('contact_email', 'info@eko.fm')); ?></p>
                    <a class="btn btn-glass btn-sm" href="<?php echo e(whatsapp_link(setting('contact_whatsapp', '0791996450'))); ?>" target="_blank">WhatsApp</a>
                </div>

                <div class="col-lg-4">
                    <h6 class="mb-3">Follow Us</h6>
                    <div class="footer-social mb-2">
                        <a href="<?php echo e(setting('social_facebook_url', 'https://www.facebook.com/share/1CK8U1M63U/')); ?>" target="_blank"><span class="material-symbols-outlined" style="font-size:16px;">public</span>Facebook</a>
                        <a href="<?php echo e(setting('social_x_url', 'https://x.com/ekofmkotido')); ?>" target="_blank"><span class="material-symbols-outlined" style="font-size:16px;">alternate_email</span>X</a>
                        <a href="<?php echo e(setting('social_instagram_url', 'https://www.instagram.com/ekofmlive?utm_source=qr&igsh=MXY3YnJ5ZGxlNGFkcQ==')); ?>" target="_blank"><span class="material-symbols-outlined" style="font-size:16px;">photo_camera</span>Instagram</a>
                        <a href="<?php echo e(setting('social_tiktok_url', 'https://www.tiktok.com/@91.2.eko.fm?_r=1&_t=ZS-94z2dBOly7A')); ?>" target="_blank"><span class="material-symbols-outlined" style="font-size:16px;">music_note</span>TikTok</a>
                        <a href="<?php echo e(setting('social_youtube_url', 'https://youtube.com/@ekofm-x2n1l?si=p2Z3IpjNiSMWvnBq')); ?>" target="_blank"><span class="material-symbols-outlined" style="font-size:16px;">smart_display</span>YouTube</a>
                    </div>
                    <small class="d-block mt-3 opacity-75">
                        &copy; <?php echo e(date('Y')); ?> EKO FM. All rights reserved. <br>
                        Crafted by <a href="https://www.almatechconsults.com/" target="_blank" rel="noopener noreferrer" style="color: inherit; text-decoration: underline;">Alma Tech Labs Inc</a>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <a class="whatsapp-float" href="<?php echo e(whatsapp_link(setting('contact_whatsapp', '0791996450'))); ?>" target="_blank" aria-label="Chat on WhatsApp">
        <span class="material-symbols-outlined">chat</span>
    </a>

    <?php if (setting('radio_player_enabled', '1') === '1'): ?>
        <?php include __DIR__ . '/player.php'; ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo e(url('assets/js/player.js')); ?>"></script>
    <script src="<?php echo e(url('assets/js/app.js')); ?>"></script>
</body>
</html>
