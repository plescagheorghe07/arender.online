<div data-cookie-banner hidden class="fixed inset-x-0 bottom-0 z-30 p-4 sm:p-6">
    <section class="w-full max-w-lg rounded-[1.75rem] bg-white p-5 shadow-[0_18px_50px_rgba(28,25,23,0.16)]" aria-label="{{ __('ui.cookie_title') }}">
        <h2 class="text-base font-semibold tracking-tight">{{ __('ui.cookie_title') }}</h2>
        <p class="mt-2 text-sm leading-6 text-stone-600">{{ __('ui.cookie_body') }}</p>
        <p class="mt-3">
            <a href="{{ route('privacy') }}" class="text-sm font-medium text-blue-800">{{ __('ui.cookie_policy') }}</a>
        </p>
        <div class="mt-4 flex flex-wrap gap-2">
            <button type="button" data-cookie-necessary class="cursor-pointer rounded-full bg-stone-100 px-4 py-2 text-sm font-medium text-stone-800">{{ __('ui.cookie_necessary') }}</button>
            <button type="button" data-cookie-accept class="cursor-pointer rounded-full bg-blue-700 px-4 py-2 text-sm font-medium text-white">{{ __('ui.cookie_accept') }}</button>
            <button type="button" data-cookie-open class="cursor-pointer rounded-full px-4 py-2 text-sm font-medium text-stone-600">{{ __('ui.cookie_settings') }}</button>
        </div>
    </section>
</div>

<div data-cookie-settings hidden class="fixed inset-0 z-50 grid place-items-end p-4 sm:place-items-center">
    <button type="button" data-cookie-close class="absolute inset-0 bg-stone-950/30" aria-label="{{ __('ui.cookie_close') }}"></button>
    <div role="dialog" aria-modal="true" aria-labelledby="cookie-settings-title" class="relative w-full max-w-md rounded-[1.75rem] bg-white p-6 shadow-[0_24px_60px_rgba(28,25,23,0.2)]">
        <h2 id="cookie-settings-title" class="text-lg font-semibold tracking-tight">{{ __('ui.cookie_settings') }}</h2>
        <div class="mt-5 rounded-2xl bg-sky-50 px-4 py-3">
            <p class="text-sm font-medium text-blue-950">{{ __('ui.cookie_necessary_label') }}</p>
            <p class="mt-1 text-sm leading-6 text-stone-600">{{ __('ui.cookie_necessary_help') }}</p>
        </div>
        <div class="mt-3 rounded-2xl bg-stone-100 px-4 py-3">
            <p class="text-sm font-medium">{{ __('ui.cookie_optional_label') }}</p>
            <p class="mt-1 text-sm leading-6 text-stone-600">{{ __('ui.cookie_optional_help') }}</p>
        </div>
        <p class="mt-4">
            <a href="{{ route('privacy') }}" class="text-sm font-medium text-blue-800">{{ __('ui.cookie_policy') }}</a>
        </p>
        <div class="mt-5 flex flex-wrap gap-2">
            <button type="button" data-cookie-save class="cursor-pointer rounded-full bg-stone-100 px-4 py-2 text-sm font-medium text-stone-800">{{ __('ui.cookie_necessary') }}</button>
            <button type="button" data-cookie-accept class="cursor-pointer rounded-full bg-blue-700 px-4 py-2 text-sm font-medium text-white">{{ __('ui.cookie_accept') }}</button>
        </div>
    </div>
</div>

<noscript>
    <div class="fixed inset-x-0 bottom-0 z-30 p-4">
        <section class="mx-auto w-full max-w-lg rounded-[1.75rem] bg-white p-5 shadow-[0_18px_50px_rgba(28,25,23,0.16)]">
            <h2 class="text-base font-semibold tracking-tight">{{ __('ui.cookie_title') }}</h2>
            <p class="mt-2 text-sm leading-6 text-stone-600">{{ __('ui.cookie_body') }}</p>
            <a href="{{ route('privacy') }}" class="mt-3 inline-flex text-sm font-medium text-blue-800">{{ __('ui.cookie_policy') }}</a>
        </section>
    </div>
</noscript>
