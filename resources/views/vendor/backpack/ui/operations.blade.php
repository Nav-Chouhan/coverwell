@extends(backpack_view('blank'))

@php

@endphp

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">Operations</span>
        </h2>
    </section>
@endsection

@section('content')
    <div class="card">
        <div class="card-header"><i class="fa fa-align-justify"></i> Visitor <small>Operations</small></div>
        <div class="card-body">
            <form class="form-inline" enctype="multipart/form-data" method="post"
                action="{{ url(config('backpack.base.route_prefix', 'admin') . '/visitor/import') }}">
                {{ csrf_field() }}
                <input name="import_file" type="file">
                <button type="submit" class="btn btn-primary ladda-button mb-2" data-style="zoom-in"><span
                        class="ladda-label"><i class="fa fa-arrow-circle-up"></i> Import</span></button>&nbsp
                <a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/visitor/export') }}"
                    target="_blank" class="btn btn-primary ladda-button mb-2" data-style="zoom-in"><span
                        class="ladda-label"><i class="fa fa-arrow-circle-down"></i> Export</span></a>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><i class="fa fa-align-justify"></i> Company <small>Operations</small></div>
        <div class="card-body">
            <form class="form-inline" enctype="multipart/form-data" method="post"
                action="{{ url(config('backpack.base.route_prefix', 'admin') . '/company/import') }}">
                {{ csrf_field() }}
                <input name="import_file" type="file">
                <button type="submit" class="btn btn-primary ladda-button mb-2" data-style="zoom-in"><span
                        class="ladda-label"><i class="fa fa-arrow-circle-up"></i> Import</span></button>&nbsp
                <a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/company/export') }}"
                    target="_blank" class="btn btn-primary ladda-button mb-2" data-style="zoom-in"><span
                        class="ladda-label"><i class="fa fa-arrow-circle-down"></i> Export</span></a>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><i class="fa fa-align-justify"></i> User <small>Operations</small></div>
        <div class="card-body">
            <form class="form-inline" enctype="multipart/form-data" method="post"
                action="{{ url(config('backpack.base.route_prefix', 'admin') . '/user/import') }}">
                {{ csrf_field() }}
                <input name="import_file" type="file">
                <button type="submit" class="btn btn-primary ladda-button mb-2" data-style="zoom-in"><span
                        class="ladda-label"><i class="fa fa-arrow-circle-up"></i> Import</span></button>&nbsp
                <a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/user/export') }}"
                    target="_blank" class="btn btn-primary ladda-button mb-2" data-style="zoom-in"><span
                        class="ladda-label"><i class="fa fa-arrow-circle-down"></i> Export</span></a>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><i class="fa fa-align-justify"></i> Visitor<small>Logs</small></div>
        <div class="card-body">
            <a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/operations/trunk-logs') }}"
                class="btn btn-primary ladda-button mb-2" data-style="zoom-in"><span class="ladda-label"><i
                        class="fa fa-refresh"></i> Trunk Logs</span></a>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><i class="fa fa-align-justify"></i> Check<small>data</small></div>
        <div class="card-body">
            <a href="{{ url(config('backpack.base.route_prefix', 'admin') . '/operations/check-data') }}"
                class="btn btn-primary ladda-button mb-2" data-style="zoom-in"><span class="ladda-label"><i
                        class="fa fa-refresh"></i> Check Data</span></a>
        </div>
    </div>
@endsection