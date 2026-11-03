@if (session('sukses') || session('gagal'))
    <div class="toast-area">
        @if (session('sukses'))
            <div class="toast" role="status">
                @include('partials.icon', ['name' => 'check-circle', 'size' => 18])
                <span>{{ session('sukses') }}</span>
                <button type="button" aria-label="Tutup notifikasi">@include('partials.icon', ['name' => 'x', 'size' => 15])</button>
            </div>
        @endif
        @if (session('gagal'))
            <div class="toast is-error" role="alert">
                @include('partials.icon', ['name' => 'alert', 'size' => 18])
                <span>{{ session('gagal') }}</span>
                <button type="button" aria-label="Tutup notifikasi">@include('partials.icon', ['name' => 'x', 'size' => 15])</button>
            </div>
        @endif
    </div>
@endif
