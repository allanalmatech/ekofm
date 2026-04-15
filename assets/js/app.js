(function () {
    function closeMenus() {
        var header = document.querySelector('.site-header');
        var panel = document.getElementById('mainNavPanel');
        var toggle = document.getElementById('menuToggle');
        var followMenu = document.querySelector('[data-follow-menu]');
        var followToggle = followMenu ? followMenu.querySelector('.follow-toggle') : null;

        if (followMenu) {
            followMenu.classList.remove('is-open');
        }
        if (followToggle) {
            followToggle.setAttribute('aria-expanded', 'false');
        }

        if (panel) {
            panel.classList.remove('is-open');
            panel.setAttribute('hidden', 'hidden');
            panel.setAttribute('aria-hidden', 'true');
        }

        if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Open menu');
        }

        document.body.classList.remove('menu-locked');

        if (header) {
            header.classList.remove('menu-open');
        }
    }

    function pjaxNavigate(url, push) {
        fetch(url, { headers: { 'X-PJAX': 'true' } })
            .then(function (res) { return res.text(); })
            .then(function (html) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var next = doc.getElementById('pjax-container');
                var current = document.getElementById('pjax-container');
                if (!next || !current) {
                    window.location.href = url;
                    return;
                }

                current.innerHTML = next.innerHTML;
                document.title = doc.title;

                if (push) {
                    history.pushState({ pjax: true }, '', url);
                }

                window.scrollTo({ top: 0, behavior: 'smooth' });
                initUi();
                document.dispatchEvent(new CustomEvent('pjax:loaded'));
            })
            .catch(function () {
                window.location.href = url;
            });
    }

    function initPjax() {
        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[data-pjax]');
            if (!link) {
                return;
            }

            var href = link.getAttribute('href');
            if (!href || href.indexOf('admin') !== -1 || href.charAt(0) === '#') {
                return;
            }

            var parsed = document.createElement('a');
            parsed.href = href;
            if (parsed.host && parsed.host !== window.location.host) {
                return;
            }

            e.preventDefault();
            closeMenus();
            pjaxNavigate(parsed.href || href, true);
        });

        window.addEventListener('popstate', function () {
            pjaxNavigate(window.location.href, false);
        });
    }

    function initMenus() {
        var panel = document.getElementById('mainNavPanel');
        var header = document.querySelector('.site-header');
        var toggle = document.getElementById('menuToggle');
        var followMenu = document.querySelector('[data-follow-menu]');
        var followToggle = followMenu ? followMenu.querySelector('.follow-toggle') : null;
        var closeButtons = panel ? panel.querySelectorAll('[data-menu-close]') : [];
        var sheet = panel ? panel.querySelector('.mobile-menu-sheet') : null;
        var brandMark = header ? header.querySelector('.brand-mark') : null;
        var primaryClose = panel ? panel.querySelector('.mobile-close') : null;
        var firstLink = panel ? panel.querySelector('.mobile-nav-link') : null;
        var lastFocusedElement = null;

        if (!panel || !header || !toggle || panel.getAttribute('data-menu-bound') === '1') {
            return;
        }

        panel.setAttribute('data-menu-bound', '1');
        panel.setAttribute('aria-hidden', 'true');
        var hideTimer = null;

        var updateMenuAnchor = function () {
            if (!panel || !sheet) {
                return;
            }

            var viewportWidth = window.innerWidth || 360;
            var viewportHeight = window.innerHeight || 640;
            var left = 10;
            var top = 84;
            var width = Math.min(390, viewportWidth - 20);

            if (toggle) {
                var toggleRect = toggle.getBoundingClientRect();
                top = Math.round(toggleRect.bottom + 10);
                width = Math.min(390, Math.max(260, Math.round(viewportWidth * 0.9)));
                left = Math.round(toggleRect.right - width);
            }

            if (!toggle && brandMark) {
                var rect = brandMark.getBoundingClientRect();
                left = Math.round(rect.left);
                top = Math.round(rect.bottom + 10);
            }

            left = Math.max(8, left);
            width = Math.min(width, viewportWidth - left - 8);
            width = Math.max(260, width);

            if (left + width > viewportWidth - 8) {
                left = Math.max(8, viewportWidth - width - 8);
            }

            var maxHeight = Math.max(220, viewportHeight - top - 8);

            panel.style.setProperty('--mobile-menu-top', top + 'px');
            panel.style.setProperty('--mobile-menu-left', left + 'px');
            panel.style.setProperty('--mobile-menu-width', width + 'px');
            panel.style.setProperty('--mobile-menu-max-height', maxHeight + 'px');
        };

        var openMenu = function () {
            if (hideTimer) {
                window.clearTimeout(hideTimer);
                hideTimer = null;
            }

            lastFocusedElement = document.activeElement;
            updateMenuAnchor();
            panel.removeAttribute('hidden');
            window.requestAnimationFrame(function () {
                panel.classList.add('is-open');
            });
            panel.setAttribute('aria-hidden', 'false');
            header.classList.add('menu-open');
            document.body.classList.add('menu-locked');
            toggle.setAttribute('aria-expanded', 'true');
            toggle.setAttribute('aria-label', 'Close menu');

            window.setTimeout(function () {
                if (primaryClose) {
                    primaryClose.focus();
                } else if (firstLink) {
                    firstLink.focus();
                }
            }, 120);
        };

        var closeMenu = function () {
            panel.classList.remove('is-open');
            panel.setAttribute('aria-hidden', 'true');

            if (hideTimer) {
                window.clearTimeout(hideTimer);
            }

            hideTimer = window.setTimeout(function () {
                panel.setAttribute('hidden', 'hidden');
            }, 380);
            header.classList.remove('menu-open');
            document.body.classList.remove('menu-locked');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Open menu');

            if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                lastFocusedElement.focus();
            } else {
                toggle.focus();
            }
        };

        toggle.addEventListener('click', function () {
            if (panel.classList.contains('is-open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        for (var i = 0; i < closeButtons.length; i++) {
            closeButtons[i].addEventListener('click', closeMenu);
        }

        window.addEventListener('resize', function () {
            if (panel.classList.contains('is-open')) {
                updateMenuAnchor();
            }
        });

        panel.addEventListener('click', function (e) {
            var link = e.target.closest('a[data-pjax]');
            if (link) {
                closeMenu();
            }
        });

        if (followMenu && followToggle) {
            followToggle.addEventListener('click', function (e) {
                e.preventDefault();
                var isOpen = followMenu.classList.toggle('is-open');
                followToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            document.addEventListener('click', function (e) {
                if (!followMenu.contains(e.target)) {
                    followMenu.classList.remove('is-open');
                    followToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeMenu();
                if (followMenu && followToggle) {
                    followMenu.classList.remove('is-open');
                    followToggle.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }

    function initNavbar() {
        var header = document.querySelector('.site-header');
        var body = document.body;
        if (!header) {
            return;
        }

        var isHome = body && body.getAttribute('data-home') === '1';

        if (window.__ekoHeaderScrollHandler) {
            window.removeEventListener('scroll', window.__ekoHeaderScrollHandler);
        }

        window.__ekoHeaderScrollHandler = function () {
            var y = window.scrollY || 0;

            if (y > 60) {
                header.classList.add('compact');
            } else {
                header.classList.remove('compact');
            }

            if (y > 24 || !isHome) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        };

        window.__ekoHeaderScrollHandler();
        window.addEventListener('scroll', window.__ekoHeaderScrollHandler, { passive: true });
    }

    function normalizePath(path) {
        if (!path) {
            return '/';
        }

        var normalized = path.replace(/\/+$/, '');
        return normalized === '' ? '/' : normalized;
    }

    function syncNavActiveState() {
        var currentPath = normalizePath(window.location.pathname || '/');
        var links = document.querySelectorAll('.nav-primary .nav-link, .mobile-nav-list .mobile-nav-link');

        if (!links.length) {
            return;
        }

        var bestMatch = null;
        var bestScore = -1;

        for (var i = 0; i < links.length; i++) {
            var href = links[i].getAttribute('href');
            if (!href || href.charAt(0) === '#') {
                continue;
            }

            var parsed = document.createElement('a');
            parsed.href = href;
            var linkPath = normalizePath(parsed.pathname || '/');
            var score = -1;

            if (linkPath === currentPath) {
                score = linkPath.length + 1000;
            } else if (linkPath !== '/' && currentPath.indexOf(linkPath + '/') === 0) {
                score = linkPath.length;
            }

            if (score > bestScore) {
                bestScore = score;
                bestMatch = linkPath;
            }
        }

        for (var j = 0; j < links.length; j++) {
            var linkHref = links[j].getAttribute('href');
            if (!linkHref || linkHref.charAt(0) === '#') {
                continue;
            }

            var linkParsed = document.createElement('a');
            linkParsed.href = linkHref;
            var navPath = normalizePath(linkParsed.pathname || '/');
            links[j].classList.toggle('active', bestMatch !== null && navPath === bestMatch);
        }
    }

    function initReveal() {
        var items = document.querySelectorAll('.reveal');
        if (!items.length) {
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in');
                    var section = entry.target.closest('.story-section');
                    if (section) {
                        section.classList.add('in-view');
                    }
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -36px 0px' });

        items.forEach(function (item) {
            observer.observe(item);
        });
    }

    function initHeroMotion() {
        var targets = document.querySelectorAll('[data-parallax]');
        if (window.__ekoParallaxHandler) {
            window.removeEventListener('scroll', window.__ekoParallaxHandler);
            window.__ekoParallaxHandler = null;
        }

        if (!targets.length) {
            return;
        }

        var ticking = false;
        var run = function () {
            var scrolled = window.scrollY || 0;
            for (var i = 0; i < targets.length; i++) {
                var speed = parseFloat(targets[i].getAttribute('data-parallax') || '0.16');
                var shift = Math.min(scrolled * speed, 52);
                targets[i].style.backgroundPosition = 'center calc(50% + ' + shift + 'px)';
            }
            ticking = false;
        };

        window.__ekoParallaxHandler = function () {
            if (!ticking) {
                window.requestAnimationFrame(run);
                ticking = true;
            }
        };

        window.addEventListener('scroll', window.__ekoParallaxHandler, { passive: true });

        run();
    }

    function initHeroSlider() {
        if (window.__ekoHeroSliderDestroy) {
            window.__ekoHeroSliderDestroy();
            window.__ekoHeroSliderDestroy = null;
        }

        var root = document.querySelector('[data-hero-slider]');
        if (!root) {
            return;
        }

        var slides = root.querySelectorAll('.hero-slide');
        if (!slides.length) {
            return;
        }

        var dots = root.querySelectorAll('[data-hero-dot]');
        var prev = root.querySelector('[data-hero-prev]');
        var next = root.querySelector('[data-hero-next]');
        var index = 0;
        var interval = null;
        var touchStartX = 0;
        var touchStartY = 0;
        var hasTouchStart = false;
        var touchThreshold = 44;

        var setActive = function (nextIndex) {
            if (nextIndex < 0) {
                nextIndex = slides.length - 1;
            }
            if (nextIndex >= slides.length) {
                nextIndex = 0;
            }

            for (var i = 0; i < slides.length; i++) {
                slides[i].classList.toggle('is-active', i === nextIndex);
            }

            for (var j = 0; j < dots.length; j++) {
                dots[j].classList.toggle('is-active', j === nextIndex);
            }

            index = nextIndex;
        };

        var stopAuto = function () {
            if (interval) {
                window.clearInterval(interval);
                interval = null;
            }
        };

        var startAuto = function () {
            stopAuto();
            if (slides.length < 2) {
                return;
            }
            interval = window.setInterval(function () {
                setActive(index + 1);
            }, 6500);
        };

        var onPrev = function () {
            setActive(index - 1);
            startAuto();
        };

        var onNext = function () {
            setActive(index + 1);
            startAuto();
        };

        var onDotClick = function () {
            var targetIndex = parseInt(this.getAttribute('data-hero-dot') || '0', 10);
            setActive(targetIndex);
            startAuto();
        };

        var onMouseEnter = function () {
            stopAuto();
        };

        var onMouseLeave = function () {
            startAuto();
        };

        var onFocusIn = function () {
            stopAuto();
        };

        var onFocusOut = function (event) {
            if (root.contains(event.relatedTarget)) {
                return;
            }
            startAuto();
        };

        var onTouchStart = function (event) {
            if (!event.touches || !event.touches.length) {
                return;
            }
            touchStartX = event.touches[0].clientX;
            touchStartY = event.touches[0].clientY;
            hasTouchStart = true;
            stopAuto();
        };

        var onTouchEnd = function (event) {
            if (!hasTouchStart) {
                return;
            }

            var touch = event.changedTouches && event.changedTouches.length ? event.changedTouches[0] : null;
            if (!touch) {
                hasTouchStart = false;
                startAuto();
                return;
            }

            var deltaX = touch.clientX - touchStartX;
            var deltaY = touch.clientY - touchStartY;
            hasTouchStart = false;

            if (Math.abs(deltaX) > touchThreshold && Math.abs(deltaX) > Math.abs(deltaY)) {
                if (deltaX > 0) {
                    setActive(index - 1);
                } else {
                    setActive(index + 1);
                }
            }

            startAuto();
        };

        var onVisibilityChange = function () {
            if (document.hidden) {
                stopAuto();
            } else {
                startAuto();
            }
        };

        if (prev) {
            prev.addEventListener('click', onPrev);
        }

        if (next) {
            next.addEventListener('click', onNext);
        }

        for (var d = 0; d < dots.length; d++) {
            dots[d].addEventListener('click', onDotClick);
        }

        root.addEventListener('mouseenter', onMouseEnter);
        root.addEventListener('mouseleave', onMouseLeave);
        root.addEventListener('focusin', onFocusIn);
        root.addEventListener('focusout', onFocusOut);
        root.addEventListener('touchstart', onTouchStart, { passive: true });
        root.addEventListener('touchend', onTouchEnd, { passive: true });
        document.addEventListener('visibilitychange', onVisibilityChange);

        setActive(0);
        startAuto();

        window.__ekoHeroSliderDestroy = function () {
            stopAuto();

            if (prev) {
                prev.removeEventListener('click', onPrev);
            }

            if (next) {
                next.removeEventListener('click', onNext);
            }

            for (var i = 0; i < dots.length; i++) {
                dots[i].removeEventListener('click', onDotClick);
            }

            root.removeEventListener('mouseenter', onMouseEnter);
            root.removeEventListener('mouseleave', onMouseLeave);
            root.removeEventListener('focusin', onFocusIn);
            root.removeEventListener('focusout', onFocusOut);
            root.removeEventListener('touchstart', onTouchStart);
            root.removeEventListener('touchend', onTouchEnd);
            document.removeEventListener('visibilitychange', onVisibilityChange);
        };
    }

    function initScrollProgress() {
        var bar = document.querySelector('.scroll-progress span');
        if (!bar) {
            return;
        }

        var update = function () {
            var total = document.documentElement.scrollHeight - window.innerHeight;
            if (total <= 0) {
                bar.style.width = '0%';
                return;
            }
            var pct = Math.max(0, Math.min(100, (window.scrollY / total) * 100));
            bar.style.width = pct + '%';
        };

        if (window.__ekoProgressHandler) {
            window.removeEventListener('scroll', window.__ekoProgressHandler);
        }

        window.__ekoProgressHandler = update;
        window.addEventListener('scroll', window.__ekoProgressHandler, { passive: true });
        update();
    }

    function initUi() {
        initNavbar();
        syncNavActiveState();
        initMenus();
        initScrollProgress();
        initReveal();
        initHeroSlider();
        initHeroMotion();
    }

    initPjax();
    initUi();
})();
