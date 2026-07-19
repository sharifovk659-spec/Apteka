@props(['headers' => []])

<div {{ $attributes->class(['admin-table-wrap']) }}>
    <table class="admin-table">
        @if(! empty($headers))
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th scope="col">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
