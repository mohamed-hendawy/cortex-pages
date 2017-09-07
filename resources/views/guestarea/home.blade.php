{{-- Master Layout --}}
@extends('cortex/foundation::guestarea.layouts.default')

{{-- Page Title --}}
@section('title')
    {{ config('app.name') }} » {{ trans('cortex/pages::common.welcome') }}
@stop

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <section class="panel panel-default">
                    <header class="panel-heading">{{ trans('cortex/pages::common.welcome') }}</header>

                    <div class="panel-body">
                        {!! trans('cortex/pages::common.welcome_body') !!}
                    </div>
                </section>
            </div>
        </div>
    </div>

@endsection
