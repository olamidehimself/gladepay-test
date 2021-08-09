@extends('layouts.app')

@section('content')
<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }} This is your unique payment link <b>http://127:0.0.1:8000/pay-me?uniq={{auth()->user()->referral_id}}</b>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Payments</div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">First</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Type</th>
                                <th scope="col">Status</th>
                                <th scope="col">Initiated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <th scope="row">{{$transaction->id}}</th>
                                    <td>{{$transaction->client_first_name}} {{$transaction->client_last_name}}</td>
                                    <td>{{$transaction->amount}}</td>
                                    <td>{{$transaction->type}}</td>
                                    <td>{{$transaction->status}}</td>
                                    <td>{{date_format($transaction->created_at,"Y/m/d H:i:s")}}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
