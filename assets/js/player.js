(function () {
    var audio = document.getElementById('global-audio');
    var toggle = document.getElementById('player-toggle');
    var volume = document.getElementById('player-volume');
    var streamLabel = document.getElementById('stream-title');
    var heroNowPlaying = document.getElementById('hero-now-playing');
    var shell = document.getElementById('live-player');
    var minBtn = document.getElementById('player-minimize');

    if (!audio || !toggle) {
        return;
    }

    var stateKey = 'ekofm_player_state';
    var source = audio.querySelector('source');
    var embedScript = audio.getAttribute('data-embed-script') || '';
    var baseUrl = audio.getAttribute('data-base-url') || window.location.origin;
    var defaultNowPlaying = audio.getAttribute('data-now-playing') || 'EKO FM Live';

    var candidates = [];
    var candidateIndex = 0;
    var resolving = false;
    var nowPlayingTimer = null;

    function readState() {
        try {
            return JSON.parse(localStorage.getItem(stateKey) || '{}');
        } catch (e) {
            return {};
        }
    }

    function saveState() {
        localStorage.setItem(stateKey, JSON.stringify({
            playing: !audio.paused,
            volume: audio.volume,
            collapsed: shell ? shell.classList.contains('is-collapsed') : false
        }));
    }

    function updateIcon() {
        var icon = toggle.querySelector('.material-symbols-outlined');
        if (icon) {
            icon.textContent = audio.paused ? 'play_arrow' : 'pause';
        }
        document.body.classList.toggle('is-playing', !audio.paused);
    }

    function normalizeUrl(url) {
        if (!url) {
            return '';
        }
        if (url.indexOf('//') === 0) {
            return window.location.protocol + url;
        }
        return url;
    }

    function addCandidate(url) {
        var clean = normalizeUrl(url);
        if (!clean) {
            return;
        }
        for (var i = 0; i < candidates.length; i++) {
            if (candidates[i] === clean) {
                return;
            }
        }
        candidates.push(clean);
    }

    function buildCandidates(url) {
        addCandidate(url);

        var parsed = normalizeUrl(url);
        var match = parsed.match(/^https?:\/\/(\d+\.\d+\.\d+\.\d+)\/(\d+)\/listen\.mp3/i);
        if (match) {
            addCandidate('http://' + match[1] + ':' + match[2] + '/listen.mp3');
            addCandidate('https://' + match[1] + ':' + match[2] + '/listen.mp3');
        }
    }

    function setStream(url) {
        if (!source) {
            return;
        }
        source.setAttribute('src', url);
        audio.load();
    }

    function tryCurrentStream() {
        if (!candidates.length) {
            return;
        }
        setStream(candidates[candidateIndex]);
    }

    function setNowPlaying(text) {
        if (streamLabel) {
            streamLabel.textContent = text;
        }
        if (heroNowPlaying) {
            heroNowPlaying.textContent = text;
        }
    }

    function refreshNowPlaying() {
        var endpoint = baseUrl + '/handlers/now_playing.php?_=' + Date.now();
        fetch(endpoint)
            .then(function (res) { return res.json(); })
            .then(function (json) {
                if (!json || !json.ok || !json.title) {
                    return;
                }
                setNowPlaying(json.title);
            })
            .catch(function () {});
    }

    function resolveFromEmbed(done) {
        if (!embedScript || resolving) {
            done();
            return;
        }

        resolving = true;
        var endpoint = baseUrl + '/handlers/radio_resolver.php?embed=' + encodeURIComponent(embedScript) + '&_=' + Date.now();

        fetch(endpoint)
            .then(function (res) { return res.json(); })
            .then(function (json) {
                if (json && json.ok && json.url) {
                    candidates = [];
                    candidateIndex = 0;
                    buildCandidates(json.url);
                    setNowPlaying(defaultNowPlaying);
                }
            })
            .catch(function () {})
            .then(function () {
                resolving = false;
                done();
            });
    }

    function startPlayback() {
        audio.play().catch(function () {
            setNowPlaying('Stream offline. Contact support team.');
        });
    }

    function handlePlayToggle() {
        if (!audio.paused) {
            audio.pause();
            return;
        }

        resolveFromEmbed(function () {
            tryCurrentStream();
            startPlayback();
        });
    }

    function bindHeroButton() {
        var heroToggle = document.getElementById('hero-player-toggle');
        if (!heroToggle || heroToggle.getAttribute('data-bound') === '1') {
            return;
        }
        heroToggle.setAttribute('data-bound', '1');
        heroToggle.addEventListener('click', handlePlayToggle);
    }

    function syncMinimizeIcon() {
        var icon = minBtn ? minBtn.querySelector('.material-symbols-outlined') : null;
        if (!shell || !icon) {
            return;
        }
        if (shell.classList.contains('is-collapsed')) {
            icon.textContent = 'expand_less';
            minBtn.setAttribute('aria-label', 'Expand player');
        } else {
            icon.textContent = 'expand_more';
            minBtn.setAttribute('aria-label', 'Collapse player');
        }
    }

    function toggleMinimize() {
        if (!shell) {
            return;
        }
        shell.classList.toggle('is-collapsed');
        syncMinimizeIcon();
        saveState();
    }

    function initFooterDocking() {
        if (!shell) {
            return;
        }

        var footer = document.querySelector('.site-footer');
        if (!footer) {
            return;
        }

        if (window.__ekoPlayerFooterDockHandler) {
            window.removeEventListener('scroll', window.__ekoPlayerFooterDockHandler);
            window.removeEventListener('resize', window.__ekoPlayerFooterDockHandler);
            window.__ekoPlayerFooterDockHandler = null;
        }

        var baseBottom = parseFloat(window.getComputedStyle(shell).bottom || '20') || 20;
        var dockGap = 8;

        var ticking = false;
        var update = function () {
            var rect = footer.getBoundingClientRect();
            var visibleFooter = Math.max(0, window.innerHeight - rect.top);

            if (visibleFooter > 0) {
                var dockedBottom = Math.max(baseBottom, visibleFooter + dockGap);
                shell.style.bottom = dockedBottom + 'px';
            } else {
                shell.style.removeProperty('bottom');
            }

            ticking = false;
        };

        window.__ekoPlayerFooterDockHandler = function () {
            if (!ticking) {
                window.requestAnimationFrame(update);
                ticking = true;
            }
        };

        window.addEventListener('scroll', window.__ekoPlayerFooterDockHandler, { passive: true });
        window.addEventListener('resize', window.__ekoPlayerFooterDockHandler);
        update();
    }

    var initial = readState();
    if (typeof initial.volume !== 'undefined') {
        audio.volume = initial.volume;
        if (volume) {
            volume.value = initial.volume;
        }
    }

    if (shell && initial.collapsed) {
        shell.classList.add('is-collapsed');
    }

    syncMinimizeIcon();

    buildCandidates(source ? source.getAttribute('src') : '');

    if (initial.playing) {
        resolveFromEmbed(function () {
            tryCurrentStream();
            startPlayback();
        });
    }

    toggle.addEventListener('click', handlePlayToggle);

    if (minBtn) {
        minBtn.addEventListener('click', toggleMinimize);
    }

    if (volume) {
        volume.addEventListener('input', function () {
            audio.volume = parseFloat(volume.value || '0.8');
            saveState();
        });
    }

    audio.addEventListener('play', function () {
        updateIcon();
        saveState();
    });

    audio.addEventListener('pause', function () {
        updateIcon();
        saveState();
    });

    audio.addEventListener('error', function () {
        if (candidateIndex < candidates.length - 1) {
            candidateIndex++;
            tryCurrentStream();
            startPlayback();
            setNowPlaying('Reconnecting live stream...');
            return;
        }

        setNowPlaying('Stream offline. Contact support team.');
    });

    document.addEventListener('pjax:loaded', bindHeroButton);
    document.addEventListener('pjax:loaded', function () {
        heroNowPlaying = document.getElementById('hero-now-playing');
        initFooterDocking();
        if (heroNowPlaying && streamLabel && streamLabel.textContent) {
            heroNowPlaying.textContent = streamLabel.textContent;
        }
    });
    bindHeroButton();
    setNowPlaying(defaultNowPlaying);
    refreshNowPlaying();
    if (nowPlayingTimer) {
        window.clearInterval(nowPlayingTimer);
    }
    nowPlayingTimer = window.setInterval(refreshNowPlaying, 60000);
    updateIcon();
    initFooterDocking();
})();
