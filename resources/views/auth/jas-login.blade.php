@extends('layouts.app')
@section('content')
<main class="login-form">
    <div class="cotainer">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <h3 class="card-header text-center">Jas Login</h3>
                    <div class="card-body">
                        <form method="POST" action="{{ route('jas-login') }}" name="hospitality-login">
                            @csrf
                            <div class="form-group mb-3">
                                <input type="text" placeholder="phone number" id="phoneNumber" class="form-control" name="phoneNumber" required autofocus>
                                @if ($errors->has('phoneNumber'))
                                <p class="text-center"><span class="text-danger">{{ $errors->first('phoneNumber') }}</span></p>
                                @endif
                            </div>
                            <div class="d-grid mx-auto">
                                <button type="submit" class="btn btn-dark btn-block">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection