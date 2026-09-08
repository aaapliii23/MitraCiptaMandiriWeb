<!-- Real-time Admin Notifications (Orders & Chat) -->
<div id="mcmNotificationContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;"></div>

<script>
(function() {
    // Avoid double initialization
    if (window.MCM_NOTIF_INITIALIZED) return;
    window.MCM_NOTIF_INITIALIZED = true;

    var adminBase = '<?php echo $adminBase; ?>';
    var lastOrderId = 0;
    var lastChatId = 0;
    var pollTimer = null;
    var isPolling = false;
    var audioCtx = null;

    // Sound toggle preference
    function isSoundEnabled() {
        return localStorage.getItem('mcm_notif_sound') !== 'disabled';
    }

    function setSoundEnabled(enabled) {
        localStorage.setItem('mcm_notif_sound', enabled ? 'enabled' : 'disabled');
        updateSoundButtonState();
    }

    // Web Audio Synthesizer: Clean, warm chimes without external media files
    function getAudioContext() {
        if (!audioCtx) {
            var AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (AudioContextClass) {
                audioCtx = new AudioContextClass();
            }
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        return audioCtx;
    }

    // Play Order Chime: Warm C Major triad arpeggio (C5 -> E5 -> G5)
    function playOrderSound() {
        if (!isSoundEnabled()) return;
        try {
            var ctx = getAudioContext();
            if (!ctx) return;
            var now = ctx.currentTime;

            var freqs = [523.25, 659.25, 783.99]; // C5, E5, G5
            freqs.forEach(function(freq, index) {
                var osc = ctx.createOscillator();
                var gain = ctx.createGain();
                
                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, now + (index * 0.1));

                gain.gain.setValueAtTime(0.001, now + (index * 0.1));
                gain.gain.exponentialRampToValueAtTime(0.18, now + (index * 0.1) + 0.04);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + (index * 0.1) + 0.45);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now + (index * 0.1));
                osc.stop(now + (index * 0.1) + 0.48);
            });
        } catch (e) {
            console.warn('Audio chime notice:', e);
        }
    }

    // Play Chat Bell: Two-tone bright notification ping (D5 -> A5)
    function playChatSound() {
        if (!isSoundEnabled()) return;
        try {
            var ctx = getAudioContext();
            if (!ctx) return;
            var now = ctx.currentTime;

            var freqs = [587.33, 880.00]; // D5, A5
            freqs.forEach(function(freq, index) {
                var osc = ctx.createOscillator();
                var gain = ctx.createGain();

                osc.type = 'triangle';
                osc.frequency.setValueAtTime(freq, now + (index * 0.08));

                gain.gain.setValueAtTime(0.001, now + (index * 0.08));
                gain.gain.exponentialRampToValueAtTime(0.15, now + (index * 0.08) + 0.03);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + (index * 0.08) + 0.35);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(now + (index * 0.08));
                osc.stop(now + (index * 0.08) + 0.38);
            });
        } catch (e) {
            console.warn('Audio chime notice:', e);
        }
    }

    // Escape HTML helper
    function esc(s) {
        return String(s || '').replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    // Render Toast Notification
    function showNotificationToast(config) {
        var container = document.getElementById('mcmNotificationContainer');
        if (!container) return;

        var toastId = 'mcm-toast-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
        var toastEl = document.createElement('div');
        toastEl.id = toastId;
        toastEl.className = 'toast show border-0 shadow-lg rounded-4 overflow-hidden mb-3 animate__animated animate__fadeInRight';
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        toastEl.style.background = '#ffffff';
        toastEl.style.minWidth = '320px';
        toastEl.style.maxWidth = '380px';

        var bgIcon = config.type === 'order' ? 'linear-gradient(135deg, #059669, #10b981)' : 'linear-gradient(135deg, #0284c7, #0ea5e9)';
        var iconClass = config.type === 'order' ? 'fas fa-shopping-bag' : 'fab fa-whatsapp';

        toastEl.innerHTML = [
            '<div class="toast-header border-0 bg-light py-2 px-3 d-flex align-items-center justify-content-between">',
                '<div class="d-flex align-items-center gap-2">',
                    '<div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="width:26px; height:26px; background:' + bgIcon + '; font-size:0.75rem;">',
                        '<i class="' + iconClass + '"></i>',
                    '</div>',
                    '<strong class="text-dark small fw-bold">' + esc(config.title) + '</strong>',
                '</div>',
                '<div class="d-flex align-items-center gap-2">',
                    '<small class="text-muted" style="font-size:0.7rem;">' + esc(config.time || 'Baru') + '</small>',
                    '<button type="button" class="btn-close btn-close-sm ms-1" data-bs-dismiss="toast" aria-label="Tutup"></button>',
                '</div>',
            '</div>',
            '<div class="toast-body p-3 bg-white">',
                '<div class="small text-secondary mb-2">' + config.body + '</div>',
                '<div class="d-flex justify-content-end gap-2 pt-1 border-top border-light">',
                    '<button type="button" class="btn btn-sm btn-light rounded-pill px-3 py-1 small" data-bs-dismiss="toast">Tutup</button>',
                    '<a href="' + esc(config.actionUrl) + '" class="btn btn-sm btn-primary rounded-pill px-3 py-1 small fw-bold js-notif-action" style="background:' + bgIcon + '; border:none;">',
                        esc(config.actionLabel),
                    '</a>',
                '</div>',
            '</div>'
        ].join('');

        container.appendChild(toastEl);

        // Bind dismiss button
        toastEl.querySelectorAll('[data-bs-dismiss="toast"]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                toastEl.classList.remove('animate__fadeInRight');
                toastEl.classList.add('animate__fadeOutRight');
                setTimeout(function() { toastEl.remove(); }, 300);
            });
        });

        // Intercept action link for SPA navigation
        var actionBtn = toastEl.querySelector('.js-notif-action');
        if (actionBtn) {
            actionBtn.addEventListener('click', function(e) {
                var targetUrl = this.getAttribute('href');
                if (typeof window.loadContent === 'function' && targetUrl.startsWith('?page=')) {
                    e.preventDefault();
                    window.loadContent(targetUrl, true);
                    toastEl.remove();
                }
            });
        }

        // Auto dismiss after 8 seconds
        setTimeout(function() {
            if (document.getElementById(toastId)) {
                toastEl.classList.remove('animate__fadeInRight');
                toastEl.classList.add('animate__fadeOutRight');
                setTimeout(function() { toastEl.remove(); }, 300);
            }
        }, 8000);
    }

    // Update Badges on Sidebar and Mobile View
    function updateSidebarBadges(data) {
        if (!data) return;

        var currentPage = new URLSearchParams(window.location.search).get('page') || 'dashboard';

        // 1. Orders badge — hide immediately if admin is already on orders page
        var orderLinks = document.querySelectorAll('a[href="?page=orders"]');
        var showOrderBadge = data.pending_orders > 0 && currentPage !== 'orders';
        orderLinks.forEach(function(link) {
            var existingBadge = link.querySelector('.js-order-badge');
            if (showOrderBadge) {
                if (!existingBadge) {
                    var badge = document.createElement('span');
                    badge.className = 'badge bg-warning text-dark rounded-pill ms-auto js-order-badge';
                    badge.style.fontSize = '0.65rem';
                    badge.textContent = data.pending_orders;
                    link.appendChild(badge);
                } else {
                    existingBadge.textContent = data.pending_orders;
                }
            } else if (existingBadge) {
                existingBadge.remove();
            }
        });

        // 2. Chat WhatsApp badge — hide immediately if admin is already on chat page
        var chatLinks = document.querySelectorAll('a[href="?page=chat"]');
        var showChatBadge = data.unread_chats > 0 && currentPage !== 'chat';
        chatLinks.forEach(function(link) {
            var existingBadge = link.querySelector('.js-chat-badge');
            if (showChatBadge) {
                if (!existingBadge) {
                    var badge = document.createElement('span');
                    badge.className = 'badge bg-danger rounded-pill ms-auto js-chat-badge';
                    badge.style.fontSize = '0.65rem';
                    badge.textContent = data.unread_chats;
                    link.appendChild(badge);
                } else {
                    existingBadge.textContent = data.unread_chats;
                }
            } else if (existingBadge) {
                existingBadge.remove();
            }
        });
    }

    // Clear badge when admin opens the relevant page
    function clearBadgesForPage(pageName) {
        if (pageName === 'chat') {
            document.querySelectorAll('.js-chat-badge').forEach(function(b) { b.remove(); });
        }
        if (pageName === 'orders') {
            document.querySelectorAll('.js-order-badge').forEach(function(b) { b.remove(); });
        }
    }

    // Expose clearBadgesForPage so scripts_navigation.php loadContent can call it
    window.MCMNotifClearBadges = clearBadgesForPage;

    // Poll server for new events
    function checkNotifications(isInitial) {
        if (isPolling) return;
        isPolling = true;

        var url = adminBase + '/actions/check_notifications.php?last_order_id=' + lastOrderId + '&last_chat_id=' + lastChatId;
        if (isInitial) url += '&init=1';

        fetch(url)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.status === 'success') {
                    // Update max IDs
                    if (data.latest_order_id) lastOrderId = data.latest_order_id;
                    if (data.latest_chat_id) lastChatId = data.latest_chat_id;

                    // Update UI Badges
                    updateSidebarBadges(data);

                    // If not initial sync, trigger notifications
                    if (!isInitial) {
                        // Handle New Orders
                        if (data.new_orders && data.new_orders.length > 0) {
                            playOrderSound();
                            data.new_orders.forEach(function(order) {
                                showNotificationToast({
                                    type: 'order',
                                    title: 'Pesanan Baru Masuk',
                                    time: order.time,
                                    body: '<strong>' + esc(order.customer_name) + '</strong> mendaftar program <strong>' + esc(order.class_name) + '</strong> (' + esc(order.amount_formatted) + ').',
                                    actionUrl: '?page=orders',
                                    actionLabel: 'Lihat Transaksi'
                                });
                            });
                        }

                        // Handle New Chat Messages
                        if (data.new_chats && data.new_chats.length > 0) {
                            playChatSound();
                            data.new_chats.forEach(function(chat) {
                                showNotificationToast({
                                    type: 'chat',
                                    title: 'Pesan Chat Baru',
                                    time: chat.time,
                                    body: 'Dari <strong>' + esc(chat.sender_label) + '</strong>: "' + esc(chat.message) + '"',
                                    actionUrl: '?page=chat&thread=' + encodeURIComponent(chat.wa_number),
                                    actionLabel: 'Buka Chat'
                                });
                            });
                        }
                    }
                }
            })
            .catch(function(err) {
                // Silently ignore network dropouts, retry on next cycle
            })
            .finally(function() {
                isPolling = false;
                scheduleNextPoll();
            });
    }

    function scheduleNextPoll() {
        clearTimeout(pollTimer);
        // If tab is hidden, throttle poll to 20 seconds, else 8 seconds
        var interval = document.hidden ? 20000 : 8000;
        pollTimer = setTimeout(function() {
            checkNotifications(false);
        }, interval);
    }

    // Visibility change handling to save resources when tab is backgrounded
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            checkNotifications(false);
        }
    });

    // Audio unlock on user interaction (modern browser policy)
    function unlockAudioOnFirstClick() {
        getAudioContext();
        document.removeEventListener('click', unlockAudioOnFirstClick);
        document.removeEventListener('keydown', unlockAudioOnFirstClick);
    }
    document.addEventListener('click', unlockAudioOnFirstClick);
    document.addEventListener('keydown', unlockAudioOnFirstClick);

    // Initial check (sync base state without popup)
    checkNotifications(true);

    // On initial hard-load, clear badge if already on the target page
    (function() {
        var initPage = new URLSearchParams(window.location.search).get('page') || 'dashboard';
        clearBadgesForPage(initPage);
    })();

    // Expose global controller for manual triggers or sound toggle
    window.MCMNotification = {
        checkNow: function() { checkNotifications(false); },
        playOrderSound: playOrderSound,
        playChatSound: playChatSound,
        isSoundEnabled: isSoundEnabled,
        setSoundEnabled: setSoundEnabled,
        clearBadgesForPage: clearBadgesForPage
    };
})();
</script>
