<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', __('admin.panel')) · {{ config('app.name') }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @fonts
            @vite(['resources/css/app.css', 'resources/js/app.js'])
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const closeMenus = () => {
                        document.querySelectorAll('[data-menu-panel]').forEach((panel) => panel.classList.add('hidden'));
                    };

                    const openDialog = (dialog) => {
                        dialog.classList.remove('hidden');
                        dialog.classList.add('flex');
                    };

                    const closeDialog = (dialog) => {
                        dialog.classList.add('hidden');
                        dialog.classList.remove('flex');
                    };

                    document.querySelectorAll('[data-add-row]').forEach((button) => {
                        button.addEventListener('click', () => {
                            const template = document.querySelector(button.getAttribute('data-add-row') ?? '');
                            const target = document.querySelector(button.getAttribute('data-target') ?? '');

                            if (!(template instanceof HTMLTemplateElement) || !(target instanceof HTMLElement)) {
                                return;
                            }

                            const html = template.innerHTML.replaceAll('__INDEX__', String(target.children.length));
                            target.insertAdjacentHTML('beforeend', html);
                            bindTabs(target);
                        });
                    });

                    const placeMenu = (button, panel) => {
                        panel.classList.remove('hidden');
                        panel.style.position = 'fixed';
                        panel.style.zIndex = '60';
                        panel.style.margin = '0';
                        panel.style.visibility = 'hidden';
                        const panelBox = panel.getBoundingClientRect();
                        const buttonBox = button.getBoundingClientRect();
                        const gap = 6;
                        const openAbove = window.innerHeight - buttonBox.bottom < panelBox.height + gap;
                        const top = openAbove
                            ? Math.max(8, buttonBox.top - panelBox.height - gap)
                            : buttonBox.bottom + gap;
                        const left = Math.max(8, Math.min(buttonBox.right - panelBox.width, window.innerWidth - panelBox.width - 8));
                        panel.style.top = `${top}px`;
                        panel.style.left = `${left}px`;
                        panel.style.visibility = '';
                    };

                    document.querySelectorAll('[data-menu-button]').forEach((button) => {
                        button.addEventListener('click', (event) => {
                            event.stopPropagation();
                            const panel = button.parentElement?.querySelector('[data-menu-panel]');
                            const wasOpen = panel instanceof HTMLElement && ! panel.classList.contains('hidden');
                            closeMenus();

                            if (panel instanceof HTMLElement && ! wasOpen) {
                                placeMenu(button, panel);
                            }
                        });
                    });

                    document.addEventListener('click', (event) => {
                        if (!(event.target instanceof Element) || ! event.target.closest('[data-menu-panel]')) {
                            closeMenus();
                        }
                    });

                    window.addEventListener('scroll', closeMenus, true);
                    window.addEventListener('resize', closeMenus);

                    document.querySelectorAll('[data-open-dialog]').forEach((button) => {
                        button.addEventListener('click', () => {
                            const dialog = document.getElementById(button.getAttribute('data-open-dialog') ?? '');

                            if (dialog instanceof HTMLElement) {
                                closeMenus();
                                openDialog(dialog);
                            }
                        });
                    });

                    document.querySelectorAll('[data-dialog]').forEach((dialog) => {
                        if (!(dialog instanceof HTMLElement)) {
                            return;
                        }

                        dialog.addEventListener('click', (event) => {
                            if (event.target === dialog) {
                                closeDialog(dialog);
                            }
                        });

                        dialog.querySelectorAll('[data-close-dialog]').forEach((button) => {
                            button.addEventListener('click', () => closeDialog(dialog));
                        });

                        if (dialog.hasAttribute('data-open')) {
                            openDialog(dialog);
                        }
                    });

                    document.addEventListener('click', (event) => {
                        const button = event.target instanceof Element ? event.target.closest('[data-remove-row]') : null;
                        button?.closest('[data-value-row]')?.remove();
                    });

                    const bindTabs = (scope) => {
                        scope.querySelectorAll('[data-tabs]').forEach((tabs) => {
                            if (tabs.dataset.bound === '1') {
                                return;
                            }

                            tabs.dataset.bound = '1';
                            const buttons = tabs.querySelector(':scope > div')?.querySelectorAll('[data-tab]') ?? [];
                            const panels = tabs.querySelectorAll(':scope > [data-tab-panel]');
                            const showTab = (name) => {
                                buttons.forEach((button) => {
                                    const on = button.getAttribute('data-tab') === name;
                                    button.classList.toggle('bg-emerald-900', on);
                                    button.classList.toggle('text-white', on);
                                    button.classList.toggle('text-stone-500', ! on);
                                });
                                panels.forEach((panel) => {
                                    const on = panel.getAttribute('data-tab-panel') === name;
                                    panel.classList.toggle('hidden', ! on);
                                    panel.classList.toggle('is-shown', on);
                                });
                            };

                            showTab(tabs.getAttribute('data-tabs-initial') || 'ro');
                            buttons.forEach((button) => {
                                button.addEventListener('click', () => showTab(button.getAttribute('data-tab') || 'ro'));
                            });
                        });
                    };

                    const bindRecordTabs = (scope) => {
                        scope.querySelectorAll('[data-record-tabs]').forEach((root) => {
                            if (root.dataset.recordBound === '1') {
                                return;
                            }

                            root.dataset.recordBound = '1';
                            const bar = root.querySelector(':scope > [data-record-bar]');
                            const buttons = bar?.querySelectorAll('[data-record-tab]') ?? [];
                            const show = (name) => {
                                buttons.forEach((button) => {
                                    const on = button.getAttribute('data-record-tab') === name;
                                    button.classList.toggle('border-emerald-900', on);
                                    button.classList.toggle('text-stone-900', on);
                                    button.classList.toggle('border-transparent', ! on);
                                    button.classList.toggle('text-stone-500', ! on);
                                });
                                Array.from(root.children).forEach((panel) => {
                                    if (! panel.hasAttribute('data-record-panel')) {
                                        return;
                                    }

                                    panel.classList.toggle('hidden', panel.getAttribute('data-record-panel') !== name);
                                });
                            };
                            const first = buttons[0]?.getAttribute('data-record-tab') || 'details';

                            show(root.getAttribute('data-record-initial') || first);
                            buttons.forEach((button) => {
                                button.addEventListener('click', () => show(button.getAttribute('data-record-tab') || first));
                            });
                        });
                    };

                    bindTabs(document);
                    bindRecordTabs(document);

                    document.querySelectorAll('[data-attachments]').forEach((box) => {
                        const input = box.querySelector('[data-attachment-input]');
                        const pending = box.querySelector('[data-attachment-pending]');
                        const zone = box.querySelector('[data-dropzone]');

                        if (! (input instanceof HTMLInputElement) || ! (pending instanceof HTMLElement) || ! (zone instanceof HTMLElement)) {
                            return;
                        }

                        const paint = () => {
                            pending.replaceChildren();
                            Array.from(input.files ?? []).forEach((file, index) => {
                                const figure = document.createElement('figure');
                                figure.className = 'relative h-24 w-24 overflow-hidden rounded-xl bg-[#f7f5f2]';
                                const img = document.createElement('img');
                                img.className = 'h-full w-full object-cover';
                                img.alt = '';
                                img.src = URL.createObjectURL(file);
                                const remove = document.createElement('button');
                                remove.type = 'button';
                                remove.className = 'absolute top-1 right-1 grid h-7 w-7 cursor-pointer place-items-center rounded-full bg-white text-lg leading-none text-red-700 shadow-sm';
                                remove.textContent = '×';
                                remove.addEventListener('click', () => {
                                    const next = new DataTransfer();
                                    Array.from(input.files ?? []).forEach((item, itemIndex) => {
                                        if (itemIndex !== index) {
                                            next.items.add(item);
                                        }
                                    });
                                    input.files = next.files;
                                    paint();
                                });
                                figure.append(img, remove);
                                pending.append(figure);
                            });
                        };

                        input.addEventListener('change', paint);
                        zone.addEventListener('dragover', (event) => {
                            event.preventDefault();
                            zone.classList.add('is-hot');
                        });
                        zone.addEventListener('dragleave', () => zone.classList.remove('is-hot'));
                        zone.addEventListener('drop', (event) => {
                            event.preventDefault();
                            zone.classList.remove('is-hot');
                            const next = new DataTransfer();
                            Array.from(input.files ?? []).forEach((file) => next.items.add(file));
                            Array.from(event.dataTransfer?.files ?? []).forEach((file) => {
                                if (file.type.startsWith('image/')) {
                                    next.items.add(file);
                                }
                            });
                            input.files = next.files;
                            paint();
                        });
                        box.addEventListener('click', (event) => {
                            const button = event.target instanceof Element ? event.target.closest('[data-remove-attachment]') : null;
                            button?.closest('[data-attachment]')?.remove();
                        });
                    });

                    document.querySelectorAll('[data-reorder]').forEach((root) => {
                        const toggle = root.querySelector('[data-reorder-toggle]');
                        const body = root.querySelector('tbody');

                        if (! (toggle instanceof HTMLInputElement) || ! (body instanceof HTMLElement)) {
                            return;
                        }

                        let dragging = null;

                        const apply = (on) => {
                            root.classList.toggle('is-reordering', on);
                            root.querySelectorAll('.reorder-handle').forEach((cell) => cell.classList.toggle('hidden', ! on));
                            root.querySelectorAll('[data-menu]').forEach((menu) => menu.classList.toggle('hidden', on));
                            body.querySelectorAll('tr[data-id]').forEach((row) => {
                                row.draggable = on;
                                if (on) {
                                    row.classList.remove('hidden');
                                } else if (row.getAttribute('data-match') === '0') {
                                    row.classList.add('hidden');
                                }
                            });
                        };

                        const persist = () => {
                            const ids = Array.from(body.querySelectorAll('tr[data-id]')).map((row) => Number(row.getAttribute('data-id')));
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

                            fetch(root.getAttribute('data-reorder') ?? '', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    Accept: 'application/json',
                                    'X-CSRF-TOKEN': token,
                                },
                                body: JSON.stringify({ ids }),
                            }).catch(() => {});
                        };

                        toggle.addEventListener('change', () => {
                            if (toggle.checked && window.location.search !== '') {
                                sessionStorage.setItem('arender-reorder', root.getAttribute('data-reorder') ?? '');
                                window.location.href = window.location.pathname;
                                return;
                            }

                            if (! toggle.checked) {
                                sessionStorage.removeItem('arender-reorder');
                            }

                            apply(toggle.checked);
                        });

                        if (sessionStorage.getItem('arender-reorder') === root.getAttribute('data-reorder')) {
                            toggle.checked = true;
                            apply(true);
                        }

                        body.addEventListener('dragstart', (event) => {
                            const row = event.target instanceof Element ? event.target.closest('tr[data-id]') : null;

                            if (! root.classList.contains('is-reordering') || ! (row instanceof HTMLElement)) {
                                return;
                            }

                            dragging = row;
                            row.classList.add('dragging');
                            event.dataTransfer?.setData('text/plain', row.getAttribute('data-id') ?? '');
                        });

                        body.addEventListener('dragover', (event) => {
                            if (! (dragging instanceof HTMLElement)) {
                                return;
                            }

                            event.preventDefault();
                            const row = event.target instanceof Element ? event.target.closest('tr[data-id]') : null;

                            if (! (row instanceof HTMLElement) || row === dragging) {
                                return;
                            }

                            const box = row.getBoundingClientRect();
                            const after = event.clientY > box.top + box.height / 2;
                            body.insertBefore(dragging, after ? row.nextSibling : row);
                        });

                        body.addEventListener('drop', (event) => {
                            event.preventDefault();
                        });

                        body.addEventListener('dragend', () => {
                            dragging?.classList.remove('dragging');
                            dragging = null;
                            persist();
                        });
                    });

                    const confirmBox = document.getElementById('admin-confirm');
                    let pendingForm = null;

                    document.querySelectorAll('form[data-confirm]').forEach((form) => {
                        form.addEventListener('submit', (event) => {
                            if (!(form instanceof HTMLFormElement) || form.dataset.confirmed === '1' || ! (confirmBox instanceof HTMLElement)) {
                                return;
                            }

                            event.preventDefault();
                            pendingForm = form;
                            const text = confirmBox.querySelector('[data-confirm-body]');

                            if (text) {
                                text.textContent = form.dataset.confirm ?? '';
                            }

                            openDialog(confirmBox);
                        });
                    });

                    confirmBox?.querySelector('[data-confirm-cancel]')?.addEventListener('click', () => {
                        pendingForm = null;

                        if (confirmBox instanceof HTMLElement) {
                            closeDialog(confirmBox);
                        }
                    });

                    confirmBox?.querySelector('[data-confirm-accept]')?.addEventListener('click', () => {
                        if (pendingForm instanceof HTMLFormElement) {
                            pendingForm.dataset.confirmed = '1';
                            pendingForm.requestSubmit();
                        }
                    });
                });
            </script>
        @endif
    </head>
    <body class="admin-app min-h-screen text-stone-900 antialiased">
        @php($staff = auth()->user())
        @php($trail = [['label' => __('admin.dashboard'), 'url' => request()->routeIs('admin.dashboard') ? null : route('admin.dashboard')]])
        @foreach ([
            'admin.orders.*' => [__('admin.orders'), 'admin.orders.index'],
            'admin.customers.*' => [__('admin.customers'), 'admin.customers.index'],
            'admin.products.*' => [__('admin.products'), 'admin.products.index'],
            'admin.news.*' => [__('admin.news'), 'admin.news.index'],
            'admin.promos.*' => [__('admin.promos'), 'admin.promos.index'],
            'admin.users.*' => [__('admin.users'), 'admin.users.index'],
        ] as $pattern => [$label, $route])
            @if (request()->routeIs($pattern))
                @php($trail[] = ['label' => $label, 'url' => request()->routeIs($route) ? null : route($route)])
            @endif
        @endforeach
        <div class="lg:grid lg:grid-cols-[232px_1fr]">
            <aside class="flex flex-col bg-white lg:sticky lg:top-0 lg:h-screen lg:overflow-y-auto">
                <div class="flex items-center justify-between px-5 py-5">
                    <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold tracking-tight text-stone-950">{{ config('app.name') }}</a>
                    <span class="rounded-full bg-[#f3f1ed] px-2 py-0.5 text-xs text-stone-500">{{ __('admin.role_'.$staff?->role?->value) }}</span>
                </div>
                <nav class="flex gap-1 overflow-x-auto px-3 pb-3 lg:block lg:px-3">
                    <p class="hidden px-3 pt-2 pb-1 text-[11px] font-medium tracking-wide text-stone-400 uppercase lg:block">{{ __('admin.group_operations') }}</p>
                    <x-admin.nav-link href="{{ route('admin.dashboard') }}" icon="dashboard" :active="request()->routeIs('admin.dashboard')">{{ __('admin.dashboard') }}</x-admin.nav-link>
                    <x-admin.nav-link href="{{ route('admin.orders.index') }}" icon="orders" :active="request()->routeIs('admin.orders.*')">{{ __('admin.orders') }}</x-admin.nav-link>
                    <x-admin.nav-link href="{{ route('admin.customers.index') }}" icon="customers" :active="request()->routeIs('admin.customers.*')">{{ __('admin.customers') }}</x-admin.nav-link>
                    @if ($staff?->managesCatalog())
                        <p class="hidden px-3 pt-4 pb-1 text-[11px] font-medium tracking-wide text-stone-400 uppercase lg:block">{{ __('admin.group_catalog') }}</p>
                        <x-admin.nav-link href="{{ route('admin.products.index') }}" icon="products" :active="request()->routeIs('admin.products.*')">{{ __('admin.products') }}</x-admin.nav-link>
                        <x-admin.nav-link href="{{ route('admin.news.index') }}" icon="news" :active="request()->routeIs('admin.news.*')">{{ __('admin.news') }}</x-admin.nav-link>
                        <x-admin.nav-link href="{{ route('admin.promos.index') }}" icon="promos" :active="request()->routeIs('admin.promos.*')">{{ __('admin.promos') }}</x-admin.nav-link>
                    @endif
                    @if ($staff?->role === \App\Models\StaffRole::Admin)
                        <p class="hidden px-3 pt-4 pb-1 text-[11px] font-medium tracking-wide text-stone-400 uppercase lg:block">{{ __('admin.group_system') }}</p>
                        <x-admin.nav-link href="{{ route('admin.users.index') }}" icon="users" :active="request()->routeIs('admin.users.*')">{{ __('admin.users') }}</x-admin.nav-link>
                    @endif
                </nav>
                <div class="mt-auto hidden items-center justify-between gap-3 px-5 py-5 text-sm lg:flex">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-stone-500 hover:text-stone-950">
                        <x-admin.icon name="site" />
                        {{ __('admin.view_site') }}
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="cursor-pointer text-stone-500 hover:text-stone-950">{{ __('admin.logout') }}</button>
                    </form>
                </div>
            </aside>
            <div class="min-w-0">
                <header class="flex items-center justify-between gap-4 px-5 pt-5 text-sm lg:px-8">
                    <nav class="flex min-w-0 items-center gap-2 text-stone-500">
                        @foreach ($trail as $crumb)
                            @if ($crumb['url'])
                                <a href="{{ $crumb['url'] }}" class="shrink-0 hover:text-stone-900">{{ $crumb['label'] }}</a>
                                <span class="text-stone-300">/</span>
                            @else
                                <span class="truncate text-stone-900">{{ $crumb['label'] }}</span>
                            @endif
                        @endforeach
                        @hasSection('crumb')
                            <span class="truncate text-stone-900">@yield('crumb')</span>
                        @endif
                    </nav>
                    <div class="flex shrink-0 items-center gap-3 text-stone-500">
                        <span class="hidden sm:inline">{{ $staff?->name }}</span>
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 hover:text-stone-900 lg:hidden">{{ __('admin.view_site') }}</a>
                        <form method="POST" action="{{ route('admin.logout') }}" class="lg:hidden">
                            @csrf
                            <button type="submit" class="cursor-pointer hover:text-stone-900">{{ __('admin.logout') }}</button>
                        </form>
                    </div>
                </header>
                <main class="px-5 pt-4 pb-10 lg:px-8">
                    @if (session('status'))
                        <p class="mb-4 rounded-2xl bg-emerald-50 px-4 py-2.5 text-sm text-emerald-950">{{ session('status') }}</p>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>

        <div id="admin-confirm" data-dialog class="fixed top-0 right-0 bottom-0 left-0 z-50 hidden items-center justify-center p-4" style="background:rgba(28,25,23,.45)" role="dialog" aria-modal="true">
            <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-semibold tracking-tight">{{ __('admin.confirm_title') }}</h2>
                <p class="mt-2 text-sm leading-6 text-stone-600" data-confirm-body></p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" data-confirm-cancel class="cursor-pointer rounded-xl px-4 py-2 text-sm font-medium text-stone-600 hover:bg-[#f3f1ed]">{{ __('admin.cancel') }}</button>
                    <button type="button" data-confirm-accept class="cursor-pointer rounded-xl bg-red-700 px-4 py-2 text-sm font-medium text-white">{{ __('admin.confirm') }}</button>
                </div>
            </div>
        </div>
    </body>
</html>
