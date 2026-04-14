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

        if (!panel || !header || !toggle || panel.getAttribute('data-menu-bound') === '1') {
            return;
        }

        panel.setAttribute('data-menu-bound', '1');
        var hideTimer = null;

        var openMenu = function () {
            if (hideTimer) {
                window.clearTimeout(hideTimer);
                hideTimer = null;
            }
            panel.removeAttribute('hidden');
            window.requestAnimationFrame(function () {
                panel.classList.add('is-open');
            });
            header.classList.add('menu-open');
            document.body.classList.add('menu-locked');
            toggle.setAttribute('aria-expanded', 'true');
            toggle.setAttribute('aria-label', 'Close menu');
        };

        var closeMenu = function () {
            panel.classList.remove('is-open');
            hideTimer = window.setTimeout(function () {
                panel.setAttribute('hidden', 'hidden');
            }, 380);
            header.classList.remove('menu-open');
            document.body.classList.remove('menu-locked');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-label', 'Open menu');
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
            if (window.innerWidth >= 992 && panel.classList.contains('is-open')) {
                closeMenu();
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

        if (prev) {
            prev.addEventListener('click', function () {
                setActive(index - 1);
            });
        }

        if (next) {
            next.addEventListener('click', function () {
                setActive(index + 1);
            });
        }

        for (var d = 0; d < dots.length; d++) {
            dots[d].addEventListener('click', function () {
                var targetIndex = parseInt(this.getAttribute('data-hero-dot') || '0', 10);
                setActive(targetIndex);
            });
        }

        root.addEventListener('mouseenter', stopAuto);
        root.addEventListener('mouseleave', startAuto);
        root.addEventListener('focusin', stopAuto);
        root.addEventListener('focusout', startAuto);

        setActive(0);
        startAuto();

        window.__ekoHeroSliderDestroy = function () {
            stopAuto();
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
