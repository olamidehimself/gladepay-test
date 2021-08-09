<?php

use App\Bank;
use App\User;
use App\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pay-me', function () {
    return view('pay')->with('banks',Bank::all());
});

Route::post('/pay-me', function (Request $request) {
    $user_id = User::where('referral_id', $request->uniq_id)->first()->id;
    $user = [
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email
    ];

    $card = [
        'card_no' => $request->cardNumber,
        'expiry_month' => $request->expiryMonth,
        'expiry_year'  => $request->expiryYear,
        'ccv' => $request->cvv
    ];

    $bank = [
        'accountnumber' => "0236007136",//$request->accountNumber,
        'bankcode' => '035'//,$request->bankCode
    ];

    if ($request->type == 'card') {
        $body = [
            "action" => "initiate",
            "paymentType" => "card",
            'account' => $bank,
            "amount" => $request->amount,
            "user" => $user,
            "card" => $card
        ];
    } else if ($request->type == 'transfer') {
        $body = [
            "action" => "charge",
            "paymentType" => "account",
            'account' => $bank,
            "amount" => $request->amount,
            "user" => $user,
        ];
    }
    

    $response = Http::withHeaders([
        'key' => '123456789',
        'mid' => 'GP0000001'
    ])->post('https://demo.api.gladepay.com/payment', $body);

    if ($response->json()['status'] == 202) {
        $transaction = Transactions::create([
            'client_first_name' => $request->first_name,
            'client_last_name' => $request->last_name,
            'email' => $request->email,
            'amount' => $request->amount,
            'type' => $request->type,
            'refID' => $response->json()['txnRef'],
            'user_id' => $user_id
        ]);
    } else {
        return response()->json(['msg' => 'Something went wrong'], 400);
    }
    

    return response()->json(['data' => $response->json(), 'transaction' => $transaction], 200);
});

Route::post('/pay-otp', function (Request $request) {

    $response = Http::withHeaders([
        'key' => '123456789',
        'mid' => 'GP0000001'
    ])->post('https://demo.api.gladepay.com/payment', [
        "action" => "validate",
        "txnRef" => $request->txnRef,
        "otp" => $request->otp,
        "validate" => $request->type == 'transfer' ? 'account': null
    ]);

    Transactions::where('refID', $request->txnRef)->update([
        'status' => 'approved'
    ]);

    return response()->json(['msg' => 'no response'], 204);
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
