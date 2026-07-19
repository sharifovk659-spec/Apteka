@if($errors->any())
    <div class="admin-alert admin-alert--error">
        <ul class="admin-alert__list">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
