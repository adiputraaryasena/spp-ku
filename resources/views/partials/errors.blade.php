@if ($errors->any())
    <div class="alert alert-danger">
        @include('partials.icon', ['name' => 'alert', 'size' => 18])
        <div>
            <strong>Periksa kembali isian berikut:</strong>
            <ul>
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
