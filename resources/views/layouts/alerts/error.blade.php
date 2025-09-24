@if(Session::has('error'))
    <div class="alert alert-danger text-xl-center" role="alert">
        <p class="mb-0">
            {{  Session::get('error') }}
        </p>
    </div>
@endif

