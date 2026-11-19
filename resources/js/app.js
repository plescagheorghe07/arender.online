document.querySelectorAll('[data-gallery]').forEach((gallery) => {
    const main = gallery.querySelector('[data-gallery-main]');

    if (! (main instanceof HTMLImageElement)) {
        return;
    }

    const thumbs = gallery.querySelectorAll('[data-gallery-thumb]');

    thumbs.forEach((thumb, index) => {
        if (index === 0) {
            thumb.classList.add('is-selected');
        }

        thumb.addEventListener('click', () => {
            const next = thumb.getAttribute('data-gallery-thumb');

            if (! next || next === main.src) {
                return;
            }

            thumbs.forEach((item) => item.classList.remove('is-selected'));
            thumb.classList.add('is-selected');
            main.style.opacity = '0.35';
            window.setTimeout(() => {
                main.src = next;
                main.style.opacity = '1';
            }, 140);
        });
    });
});

document.querySelectorAll('[data-order-form]').forEach((form) => {
    const total = form.querySelector('[data-order-total]');
    const days = form.querySelector('[data-order-days]');

    if (! (total instanceof HTMLElement) || ! (days instanceof HTMLSelectElement)) {
        return;
    }

    const formatTotal = (amount) => `${Math.round(amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ')} MDL`;

    const renderTotal = () => {
        const selected = days.selectedOptions[0];
        let amount = Number(selected instanceof HTMLOptionElement ? selected.dataset.price : 0) || 0;

        form.querySelectorAll('[data-order-addon]').forEach((addon) => {
            if (addon instanceof HTMLInputElement && addon.checked) {
                amount += Number(addon.dataset.price) || 0;
            }
        });

        total.textContent = formatTotal(amount);
    };

    days.addEventListener('change', renderTotal);
    form.querySelectorAll('[data-order-addon]').forEach((addon) => {
        addon.addEventListener('change', renderTotal);
    });
    renderTotal();
});

const revealNodes = document.querySelectorAll('[data-reveal]');

if (revealNodes.length > 0 && document.documentElement.classList.contains('motion') && 'IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (! entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-in');
            revealObserver.unobserve(entry.target);
        });
    }, { rootMargin: '0px 0px -32px 0px', threshold: 0.01 });

    revealNodes.forEach((node) => revealObserver.observe(node));
} else {
    revealNodes.forEach((node) => node.classList.add('is-in'));
}

const cookieBanner = document.querySelector('[data-cookie-banner]');
const cookieSettings = document.querySelector('[data-cookie-settings]');
const cookieName = 'arender_consent';
const cookieVersion = 'v1';
const cookieAge = 60 * 60 * 24 * 180;

const readConsent = () => {
    const row = document.cookie.split('; ').find((item) => item.startsWith(`${cookieName}=`));

    if (! row) {
        return null;
    }

    const value = decodeURIComponent(row.slice(cookieName.length + 1));

    return value === `${cookieVersion}.necessary` || value === `${cookieVersion}.all` ? value : null;
};

const writeConsent = (choice) => {
    const secure = window.location.protocol === 'https:' ? '; Secure' : '';
    document.cookie = `${cookieName}=${cookieVersion}.${choice}; Max-Age=${cookieAge}; Path=/; SameSite=Lax${secure}`;
    cookieBanner?.setAttribute('hidden', '');
    cookieSettings?.setAttribute('hidden', '');
};

if (cookieBanner instanceof HTMLElement && ! readConsent()) {
    cookieBanner.removeAttribute('hidden');
}

cookieBanner?.querySelector('[data-cookie-accept]')?.addEventListener('click', () => writeConsent('all'));
cookieBanner?.querySelector('[data-cookie-necessary]')?.addEventListener('click', () => writeConsent('necessary'));
cookieBanner?.querySelector('[data-cookie-open]')?.addEventListener('click', () => {
    cookieBanner.setAttribute('hidden', '');
    cookieSettings?.removeAttribute('hidden');
});

cookieSettings?.querySelectorAll('[data-cookie-accept]').forEach((button) => {
    button.addEventListener('click', () => writeConsent('all'));
});

cookieSettings?.querySelector('[data-cookie-save]')?.addEventListener('click', () => writeConsent('necessary'));

const closeCookieSettings = () => {
    cookieSettings?.setAttribute('hidden', '');

    if (! readConsent()) {
        cookieBanner?.removeAttribute('hidden');
    }
};

cookieSettings?.querySelectorAll('[data-cookie-close]').forEach((button) => {
    button.addEventListener('click', closeCookieSettings);
});

document.querySelectorAll('[data-cookie-manage]').forEach((button) => {
    button.addEventListener('click', () => {
        cookieBanner?.setAttribute('hidden', '');
        cookieSettings?.removeAttribute('hidden');
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && cookieSettings instanceof HTMLElement && ! cookieSettings.hasAttribute('hidden')) {
        closeCookieSettings();
    }
});

const contactToggle = document.querySelector('[data-contact-toggle]');
const contactPanel = document.querySelector('[data-contact-panel]');

if (contactToggle instanceof HTMLButtonElement && contactPanel instanceof HTMLElement) {
    contactToggle.addEventListener('click', () => {
        const willOpen = contactPanel.hasAttribute('hidden');
        contactPanel.toggleAttribute('hidden');
        contactToggle.setAttribute('aria-expanded', String(willOpen));
    });
}

const root = document.querySelector('[data-minigame]');

if (root instanceof HTMLElement) {
    const canvas = root.querySelector('[data-board]');
    const livesNode = root.querySelector('[data-lives]');
    const levelNode = root.querySelector('[data-level]');
    const scoreNode = root.querySelector('[data-score]');
    const copyNode = document.querySelector('#game-copy');
    const copy = copyNode instanceof HTMLElement ? JSON.parse(copyNode.textContent || '{}') : {};
    const alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    const blockedKey = 'arender_game_blocked_until';
    const lossesKey = 'arender_game_losses';
    const levels = [
        { dur: 700, need: 6, spd: 7, spawn: 30, good: ['🎮'], bad: ['💣'], name: copy.game_lvl1_name, sub: copy.game_lvl1_sub },
        { dur: 750, need: 8, spd: 8.5, spawn: 25, good: ['🎮', '🔊'], bad: ['💣', '❌'], name: copy.game_lvl2_name, sub: copy.game_lvl2_sub },
        { dur: 800, need: 10, spd: 10, spawn: 20, good: ['🎮', '🔊', '🧹'], bad: ['💣', '❌', '🗑️'], name: copy.game_lvl3_name, sub: copy.game_lvl3_sub },
        { dur: 850, need: 12, spd: 11.5, spawn: 15, good: ['🎮', '🔊', '🧹', '💨'], bad: ['💣', '❌', '🗑️', '⚡'], name: copy.game_lvl4_name, sub: copy.game_lvl4_sub },
        { dur: 1000, need: 15, spd: 13, spawn: 10, good: ['🎮', '🔊', '🧹', '💨', '🥽'], bad: ['💣', '❌', '🗑️', '⚡', '🔥'], name: copy.game_lvl5_name, sub: copy.game_lvl5_sub },
    ];

    if (canvas instanceof HTMLCanvasElement && livesNode && levelNode && scoreNode) {
        const context = canvas.getContext('2d');
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
        const font = getComputedStyle(document.body).fontFamily || 'sans-serif';
        const state = {
            W: 360,
            H: 500,
            level: 0,
            lives: 3,
            caught: 0,
            items: [],
            particles: [],
            bx: 180,
            raf: 0,
            frame: 0,
            spawnT: 0,
            mode: 'idle',
            promoCode: '',
        };
        let blockedTimer = 0;

        const stored = (key) => {
            try {
                return parseInt(localStorage.getItem(key) ?? '0', 10) || 0;
            } catch {
                return 0;
            }
        };

        const remember = (key, value) => {
            try {
                localStorage.setItem(key, String(value));
            } catch {
                // The original game keeps the lock in the browser.
            }
        };

        const clock = (until) => {
            const seconds = Math.max(0, Math.ceil((until - Date.now()) / 1000));
            const minutes = Math.floor(seconds / 60);

            return `${minutes}:${String(seconds % 60).padStart(2, '0')}`;
        };

        const roundRect = (x, y, width, height, radius) => {
            if (!context) {
                return;
            }

            context.beginPath();
            context.moveTo(x + radius, y);
            context.lineTo(x + width - radius, y);
            context.quadraticCurveTo(x + width, y, x + width, y + radius);
            context.lineTo(x + width, y + height - radius);
            context.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            context.lineTo(x + radius, y + height);
            context.quadraticCurveTo(x, y + height, x, y + height - radius);
            context.lineTo(x, y + radius);
            context.quadraticCurveTo(x, y, x + radius, y);
        };

        const syncHud = () => {
            const level = levels[Math.min(state.level, levels.length - 1)];
            livesNode.textContent = Array.from({ length: 3 }, (_, index) => (index < state.lives ? '❤️' : '🖤')).join('');
            levelNode.textContent = `${copy.game_hud_level ?? ''}${state.level + 1} · ${level.name ?? ''}`;
            scoreNode.textContent = `${state.caught} / ${level.need}`;
        };

        const resize = () => {
            const width = Math.min(Math.max(root.clientWidth, 320), 880);
            const ratio = window.devicePixelRatio || 1;
            state.W = width;
            state.H = 500;
            canvas.width = width * ratio;
            canvas.height = state.H * ratio;
            canvas.style.width = `${width}px`;
            canvas.style.height = `${state.H}px`;
            context?.setTransform(ratio, 0, 0, ratio, 0, 0);
            if (state.mode !== 'playing') {
                state.bx = width / 2;
            }
        };

        const paintIdle = () => {
            if (!context) {
                return;
            }

            context.fillStyle = '#14102a';
            context.fillRect(0, 0, state.W, state.H);
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.fillStyle = 'rgba(124,58,237,.12)';
            [[0.1, 0.1], [0.9, 0.15], [0.5, 0.05], [0.2, 0.8], [0.8, 0.75], [0.05, 0.5], [0.95, 0.5], [0.5, 0.9]].forEach(([x, y], index) => {
                context.beginPath();
                context.arc(x * state.W, y * state.H, 50 + index * 4, 0, Math.PI * 2);
                context.fill();
            });
            context.font = '56px serif';
            context.fillText('🎯', state.W / 2, state.H / 2 - 150);
            context.fillStyle = '#fff';
            context.font = `bold 26px ${font}`;
            context.fillText(copy.game_start_title ?? '', state.W / 2, state.H / 2 - 96);
            context.fillStyle = 'rgba(255,255,255,.5)';
            context.font = `16px ${font}`;
            context.fillText(copy.game_start_sub1 ?? '', state.W / 2, state.H / 2 - 66);
            context.fillText(copy.game_start_sub2 ?? '', state.W / 2, state.H / 2 - 44);
            context.fillStyle = '#f5c842';
            context.font = `bold 16px ${font}`;
            context.fillText(copy.game_start_promo ?? '', state.W / 2, state.H / 2 - 16);
            context.fillStyle = '#7c3aed';
            roundRect(state.W / 2 - 85, state.H / 2 + 5, 170, 54, 27);
            context.fill();
            context.fillStyle = '#fff';
            context.font = `bold 20px ${font}`;
            context.fillText(copy.game_start_btn ?? '', state.W / 2, state.H / 2 + 34);
            context.font = '28px serif';
            context.fillText('🎮  🔊  🧹  💨  🥽', state.W / 2, state.H / 2 + 96);
            context.fillStyle = 'rgba(255,255,255,.28)';
            context.font = `15px ${font}`;
            context.fillText(copy.game_start_hint ?? '', state.W / 2, state.H / 2 + 128);
            context.fillStyle = 'rgba(239,68,68,.45)';
            context.font = `14px ${font}`;
            context.fillText(copy.game_start_attempts ?? '', state.W / 2, state.H / 2 + 152);
        };

        const paintIntro = () => {
            if (!context) {
                return;
            }

            const level = levels[state.level];
            const first = state.level === 0;
            context.fillStyle = '#14102a';
            context.fillRect(0, 0, state.W, state.H);
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.font = '38px serif';
            context.fillText(first ? '🎯' : '⭐', state.W / 2, state.H / 2 - 150);
            context.fillStyle = first ? '#f5c842' : '#22c55e';
            context.font = `bold ${first ? 16 : 22}px ${font}`;
            context.fillText(first ? (copy.game_start_promo ?? '') : (copy.game_lvl_cleared ?? ''), state.W / 2, state.H / 2 - 108);
            context.fillStyle = '#fff';
            context.font = `bold 22px ${font}`;
            context.fillText(`${copy.game_lvl_label ?? ''} ${state.level + 1} · ${level.name}`, state.W / 2, state.H / 2 - 70);
            context.fillStyle = 'rgba(255,255,255,.5)';
            context.font = `16px ${font}`;
            context.fillText(level.sub ?? '', state.W / 2, state.H / 2 - 44);
            context.fillStyle = 'rgba(34,197,94,.12)';
            roundRect(state.W / 2 - 120, state.H / 2 - 30, 240, 38, 10);
            context.fill();
            context.fillStyle = '#86efac';
            context.fillText(`${copy.game_lvl_catch ?? ''}  ${level.good.join('  ')}`, state.W / 2, state.H / 2 - 10);
            context.fillStyle = 'rgba(239,68,68,.12)';
            roundRect(state.W / 2 - 120, state.H / 2 + 12, 240, 38, 10);
            context.fill();
            context.fillStyle = '#fca5a5';
            context.fillText(`${copy.game_lvl_avoid ?? ''}  ${level.bad.join('  ')}`, state.W / 2, state.H / 2 + 32);
            context.fillStyle = 'rgba(255,255,255,.3)';
            context.font = `15px ${font}`;
            context.fillText(copy.game_lvl_lives ?? '', state.W / 2, state.H / 2 + 64);
            context.fillStyle = '#7c3aed';
            roundRect(state.W / 2 - 75, state.H / 2 + 80, 150, 50, 25);
            context.fill();
            context.fillStyle = '#fff';
            context.font = `bold 18px ${font}`;
            context.fillText(first ? (copy.game_lvl_btn_start ?? '') : (copy.game_lvl_btn_next ?? ''), state.W / 2, state.H / 2 + 107);
        };

        const paintResult = (won) => {
            if (!context) {
                return;
            }

            context.fillStyle = '#14102a';
            context.fillRect(0, 0, state.W, state.H);
            context.textAlign = 'center';
            context.textBaseline = 'middle';

            if (won) {
                context.font = '52px serif';
                context.fillText('🏆', state.W / 2, state.H / 2 - 152);
                context.fillStyle = '#f5c842';
                context.font = `bold 26px ${font}`;
                context.fillText(copy.game_win_title ?? '', state.W / 2, state.H / 2 - 104);
                context.fillStyle = '#fff';
                context.font = `17px ${font}`;
                context.fillText(copy.game_win_sub ?? '', state.W / 2, state.H / 2 - 72);
                context.fillStyle = 'rgba(124,58,237,.25)';
                roundRect(state.W / 2 - 110, state.H / 2 - 54, 220, 56, 14);
                context.fill();
                context.strokeStyle = '#7c3aed';
                context.lineWidth = 2;
                roundRect(state.W / 2 - 110, state.H / 2 - 54, 220, 56, 14);
                context.stroke();
                context.fillStyle = '#f5c842';
                context.font = `bold 32px ${font}`;
                context.fillText(state.promoCode, state.W / 2, state.H / 2 - 24);
                context.fillStyle = 'rgba(255,255,255,.4)';
                context.font = `15px ${font}`;
                context.fillText(copy.game_win_hint1 ?? '', state.W / 2, state.H / 2 + 14);
                context.fillText(copy.game_win_hint2 ?? '', state.W / 2, state.H / 2 + 34);
                context.fillStyle = 'rgba(255,255,255,.08)';
                roundRect(state.W / 2 - 90, state.H / 2 + 50, 180, 50, 25);
                context.fill();
                context.fillStyle = 'rgba(255,255,255,.6)';
                context.font = `bold 16px ${font}`;
                context.fillText(copy.game_win_again ?? '', state.W / 2, state.H / 2 + 77);
                return;
            }

            context.font = '48px serif';
            context.fillText('💀', state.W / 2, state.H / 2 - 120);
            context.fillStyle = '#ef4444';
            context.font = `bold 24px ${font}`;
            context.fillText(copy.game_dead_title ?? '', state.W / 2, state.H / 2 - 74);
            context.fillStyle = 'rgba(255,255,255,.5)';
            context.font = `17px ${font}`;
            context.fillText(`${copy.game_dead_sub ?? ''} ${state.level + 1} ${copy.game_dead_of ?? ''} 5`, state.W / 2, state.H / 2 - 42);
            context.fillStyle = 'rgba(255,255,255,.3)';
            context.font = `16px ${font}`;
            context.fillText(copy.game_dead_hint ?? '', state.W / 2, state.H / 2 - 16);
            context.fillStyle = '#7c3aed';
            roundRect(state.W / 2 - 80, state.H / 2 + 12, 160, 50, 25);
            context.fill();
            context.fillStyle = '#fff';
            context.font = `bold 18px ${font}`;
            context.fillText(copy.game_dead_btn ?? '', state.W / 2, state.H / 2 + 39);
        };

        const paintBlocked = (label) => {
            if (!context) {
                return;
            }

            context.fillStyle = '#14102a';
            context.fillRect(0, 0, state.W, state.H);
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.font = '52px serif';
            context.fillText('⏳', state.W / 2, state.H / 2 - 130);
            context.fillStyle = '#ef4444';
            context.font = `bold 24px ${font}`;
            context.fillText(copy.game_blocked_title ?? '', state.W / 2, state.H / 2 - 78);
            context.fillStyle = 'rgba(255,255,255,.5)';
            context.font = `16px ${font}`;
            context.fillText(copy.game_blocked_sub ?? '', state.W / 2, state.H / 2 - 42);
            context.fillStyle = 'rgba(124,58,237,.2)';
            roundRect(state.W / 2 - 90, state.H / 2 - 24, 180, 56, 14);
            context.fill();
            context.strokeStyle = 'rgba(124,58,237,.5)';
            context.lineWidth = 1.5;
            roundRect(state.W / 2 - 90, state.H / 2 - 24, 180, 56, 14);
            context.stroke();
            context.fillStyle = '#f5c842';
            context.font = `bold 34px ${font}`;
            context.fillText(label, state.W / 2, state.H / 2 + 4);
            context.fillStyle = 'rgba(255,255,255,.3)';
            context.font = `15px ${font}`;
            context.fillText(copy.game_blocked_hint ?? '', state.W / 2, state.H / 2 + 56);
        };

        const paintPlaying = () => {
            if (!context) {
                return;
            }

            const level = levels[state.level];
            const progress = state.frame / level.dur;
            context.fillStyle = '#14102a';
            context.fillRect(0, 0, state.W, state.H);
            context.fillStyle = 'rgba(255,255,255,.07)';
            context.fillRect(0, 0, state.W, 5);
            context.fillStyle = progress < 0.65 ? '#7c3aed' : progress < 0.85 ? '#f5c842' : '#ef4444';
            context.fillRect(0, 0, state.W * (1 - progress), 5);
            context.textAlign = 'center';
            context.textBaseline = 'middle';

            state.items.forEach((item) => {
                context.globalAlpha = 1;
                context.font = `${item.sz}px serif`;
                context.fillText(item.em, item.x, item.y);
                if (!item.good) {
                    context.globalAlpha = 0.12;
                    context.fillStyle = '#ef4444';
                    context.beginPath();
                    context.arc(item.x, item.y, item.sz / 2 + 4, 0, Math.PI * 2);
                    context.fill();
                    context.fillStyle = '#fff';
                }
            });

            state.particles.forEach((particle) => {
                context.globalAlpha = particle.life / 20;
                context.font = '16px serif';
                context.fillText(particle.em, particle.x, particle.y);
            });

            context.globalAlpha = 1;
            const basketY = state.H - 44;
            context.fillStyle = 'rgba(124,58,237,.75)';
            roundRect(state.bx - 41, basketY, 82, 14, 8);
            context.fill();
            context.strokeStyle = 'rgba(167,139,250,.6)';
            context.lineWidth = 1.5;
            roundRect(state.bx - 41, basketY, 82, 14, 8);
            context.stroke();
            context.font = '18px serif';
            context.fillText('🧺', state.bx, basketY + 8);
            context.font = `bold 16px ${font}`;
            context.fillStyle = 'rgba(255,255,255,.22)';
            context.textAlign = 'right';
            context.fillText(`${state.caught} / ${level.need}`, state.W - 10, 20);
        };

        const burst = (x, y, good) => {
            for (let index = 0; index < 5; index += 1) {
                state.particles.push({
                    x: x + (Math.random() - 0.5) * 16,
                    y,
                    vx: (Math.random() - 0.5) * 2,
                    em: good ? '✨' : '💥',
                    life: 20,
                });
            }
        };

        const code = () => Array.from({ length: 4 }, () => alphabet[Math.floor(Math.random() * alphabet.length)]).join('');

        const finish = (won) => {
            if (state.raf) {
                cancelAnimationFrame(state.raf);
                state.raf = 0;
            }

            if (won) {
                remember(lossesKey, 0);
                state.mode = 'win';
                state.promoCode = code();
                syncHud();
                paintResult(true);
                fetch(root.dataset.rewardUrl ?? '', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({ code: state.promoCode }),
                }).catch(() => {});
                return;
            }

            const losses = stored(lossesKey) + 1;
            remember(lossesKey, losses);

            if (losses >= 3) {
                const until = Date.now() + 3600000;
                remember(blockedKey, until);
                remember(lossesKey, 0);
                state.mode = 'dead';
                syncHud();
                paintBlocked(clock(until));
                watchBlock(until);
                return;
            }

            state.mode = 'gameover';
            syncHud();
            paintResult(false);
        };

        const frame = () => {
            const level = levels[state.level];
            state.frame += 1;
            state.spawnT += 1;

            if (state.spawnT >= level.spawn) {
                state.spawnT = 0;
                const good = Math.random() > 0.4;
                const pool = good ? level.good : level.bad;
                state.items.push({
                    x: 24 + Math.random() * (state.W - 48),
                    y: -24,
                    good,
                    em: pool[Math.floor(Math.random() * pool.length)],
                    spd: level.spd * (0.85 + Math.random() * 0.3),
                    sz: 30 + Math.floor(Math.random() * 8),
                });
            }

            const basketY = state.H - 44;

            for (let index = state.items.length - 1; index >= 0; index -= 1) {
                const item = state.items[index];
                item.y += item.spd;
                const caught = item.y + 16 >= basketY && item.y - 16 < basketY + 14 && item.x >= state.bx - 49 && item.x <= state.bx + 49;

                if (caught) {
                    if (item.good) {
                        state.caught += 1;
                        burst(item.x, item.y, true);
                    } else {
                        state.lives -= 1;
                        burst(item.x, item.y, false);
                        state.items.splice(index, 1);
                        syncHud();
                        if (state.lives <= 0) {
                            finish(false);
                            return;
                        }
                    }
                    if (item.good) {
                        state.items.splice(index, 1);
                        syncHud();
                    }
                    continue;
                }

                if (item.y > state.H + 32) {
                    state.items.splice(index, 1);
                }
            }

            for (let index = state.particles.length - 1; index >= 0; index -= 1) {
                const particle = state.particles[index];
                particle.life -= 1;
                particle.y -= 1.5;
                particle.x += particle.vx;
                if (particle.life <= 0) {
                    state.particles.splice(index, 1);
                }
            }

            if (state.caught >= level.need) {
                if (state.level >= levels.length - 1) {
                    finish(true);
                    return;
                }

                state.mode = 'intro';
                state.level += 1;
                if (state.raf) {
                    cancelAnimationFrame(state.raf);
                    state.raf = 0;
                }
                syncHud();
                paintIntro();
                return;
            }

            if (state.frame >= level.dur) {
                finish(false);
                return;
            }

            paintPlaying();
            state.raf = requestAnimationFrame(frame);
        };

        const beginLevel = () => {
            if (state.raf) {
                cancelAnimationFrame(state.raf);
            }

            state.caught = 0;
            state.items = [];
            state.particles = [];
            state.frame = 0;
            state.spawnT = 0;
            state.mode = 'playing';
            syncHud();
            state.raf = requestAnimationFrame(frame);
        };

        const showIntro = (level) => {
            state.level = level;
            state.mode = 'intro';
            syncHud();
            paintIntro();
        };

        const boot = () => {
            resize();
            const until = stored(blockedKey);
            if (until > Date.now()) {
                state.mode = 'dead';
                syncHud();
                paintBlocked(clock(until));
                watchBlock(until);
                return;
            }

            state.mode = 'idle';
            syncHud();
            paintIdle();
        };

        const watchBlock = (until) => {
            window.clearInterval(blockedTimer);
            blockedTimer = window.setInterval(() => {
                const left = until - Date.now();
                if (left <= 0) {
                    window.clearInterval(blockedTimer);
                    remember(blockedKey, 0);
                    remember(lossesKey, 0);
                    boot();
                    return;
                }

                if (state.mode === 'dead') {
                    paintBlocked(clock(until));
                }
            }, 1000);
        };

        const pointer = (event) => {
            const rect = canvas.getBoundingClientRect();
            const x = event.clientX - rect.left;
            const y = event.clientY - rect.top;
            state.bx = Math.max(41, Math.min(state.W - 41, x));

            if (state.mode === 'playing') {
                return;
            }

            if (state.mode === 'idle' && y > state.H * 0.42) {
                state.level = 0;
                state.lives = 3;
                showIntro(0);
                return;
            }

            if (state.mode === 'intro' && y > state.H * 0.58) {
                beginLevel();
                return;
            }

            if (state.mode === 'gameover' && y > state.H * 0.5) {
                state.lives = 3;
                showIntro(state.level);
                return;
            }

            if (state.mode === 'win' && y > state.H * 0.58) {
                state.level = 0;
                state.lives = 3;
                state.caught = 0;
                state.mode = 'idle';
                syncHud();
                paintIdle();
            }
        };

        canvas.addEventListener('pointermove', (event) => {
            const rect = canvas.getBoundingClientRect();
            state.bx = Math.max(41, Math.min(state.W - 41, event.clientX - rect.left));
        });
        canvas.addEventListener('pointerdown', pointer);
        window.addEventListener('resize', () => {
            resize();
            if (state.mode === 'idle') {
                paintIdle();
            } else if (state.mode === 'intro') {
                paintIntro();
            } else if (state.mode === 'gameover') {
                paintResult(false);
            } else if (state.mode === 'win') {
                paintResult(true);
            } else if (state.mode === 'dead') {
                paintBlocked(clock(stored(blockedKey)));
            }
        });
        boot();
    }
}
