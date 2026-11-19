@extends('layouts.app')

@section('title', __('game.game_start_title'))
@section('description', __('game.game_start_sub1'))
@section('robots', 'noindex, follow')

@section('content')
    <div
        data-reveal
        class="reveal-late mx-auto max-w-3xl"
        data-minigame
        data-reward-url="{{ route('minigame.reward') }}"
    >
        <div class="overflow-hidden rounded-[2rem] bg-[#14102a] text-white shadow-xl shadow-stone-900/10">
            <div class="flex items-center justify-between gap-3 px-4 py-3 text-sm text-white/80">
                <p data-lives>❤️❤️❤️</p>
                <p data-level>{{ __('game.game_hud_level') }}1 · {{ __('game.game_lvl1_name') }}</p>
                <p data-score>0 / 6</p>
            </div>
            <canvas data-board class="block w-full touch-none" height="500"></canvas>
        </div>
    </div>
    <script type="application/json" id="game-copy">@json(__('game'))</script>
@endsection
