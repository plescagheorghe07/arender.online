@if ($errors->any())
    <div class="rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-800">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
