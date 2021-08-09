@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form onsubmit="submitPayment(event)" id="paymentForm">

                        <div class="form-group" >
                            <label for="exampleInputEmail1">First Name</label>
                            <input type="text" class="form-control" aria-describedby="first_name" name="first_name" required
                                placeholder="Enter first name">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">Last Name</label>
                            <input type="text" class="form-control" aria-describedby="last_name" name="last_name" required
                                placeholder="Enter last name">
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="email" required
                                placeholder="Enter email">
                            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Amount</label>
                            <input type="text" class="form-control" id="exampleInputPassword1" placeholder="amount" name="amount" required>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="inlineRadio1" value="card" onclick="displayCardForm()">
                            <label class="form-check-label" for="inlineRadio1">Card</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="inlineRadio2" value="transfer" onclick="displayTransferForm()">
                            <label class="form-check-label" for="inlineRadio2">Bank Transfer</label>
                        </div>
                        <div class="form-row my-3 bank" style="display: none">
                            <div class="col-6">
                                <select id="inputState" class="form-control" name="bankCode">
                                    <option hidden readonly value="">Select Bank</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{$bank->code}}">{{$bank->name}}</option>    
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="accountNumber"
                                placeholder="Enter your account number">
                            </div>
                        </div>
                        <div class="form-row my-3 card_charge" style="display: none">
                            <div class="col-6">
                                <input type="text" class="form-control" placeholder="Card Number" name="cardNumber">
                            </div>
                            <div class="col-2">
                                <input type="text" class="form-control" placeholder="Expiry Month" name="expMonth">
                            </div>
                            <div class="col-2">
                                <input type="text" class="form-control" placeholder="Expiry Year" name="expYear">
                            </div>
                            <div class="col-2">
                                <input type="text" class="form-control" placeholder="CVV" name="cvv">
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary payment-button">Submit</button>
                        </div>
                    </form>

                    <form onsubmit="submitOTP(event)" id="otpForm" style="display: none">

                        <div class="form-group" >
                            <label for="exampleInputEmail1">Enter OTP</label>
                            <input type="text" class="form-control" aria-describedby="enter OTP" name="otp"
                                placeholder="Enter first name">
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary otp-button">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function displayCardForm() {
        document.querySelector(".card_charge").style.display = 'flex';
        document.querySelector(".bank").style.display = 'none';
    }

    function displayTransferForm() {
        document.querySelector(".card_charge").style.display = 'none';
        document.querySelector(".bank").style.display = 'flex';
    }

    function submitPayment(e) {
        e.preventDefault();
        document.querySelector('.payment-button').disabled = true
        document.querySelector('.payment-button').innerHTML = 'Submitting'


        let url_string = window.location.href
        let url = new URL(url_string);
        let uniq_id = url.searchParams.get("uniq");
        let first_name = document.getElementsByName('first_name')[0].value
        let last_name = document.getElementsByName('last_name')[0].value
        let email = document.getElementsByName('email')[0].value
        let amount = document.getElementsByName('amount')[0].value
        let bankCode = document.getElementsByName('bankCode')[0].value
        let accountNumber = document.getElementsByName('accountNumber')[0].value
        let cardNumber = document.getElementsByName('cardNumber')[0].value
        let expMonth = document.getElementsByName('expMonth')[0].value;
        let expYear = document.getElementsByName('expYear')[0].value;
        let cvv = document.getElementsByName('cvv')[0].value;
        let type = document.getElementsByName('type')[0].value;
        
        axios.post('/pay-me', {
            first_name,
            last_name,
            email,
            amount,
            bankCode,
            accountNumber,
            cardNumber,
            expMonth,
            expYear,
            cvv,
            type,
            uniq_id
        }).then(res => {
            if (res.data.data.status == 202) {
                if (type == 'transfer') {
                    alert(res.data.data.message)
                } else {
                    alert('Please enter your pin')
                }
                document.getElementById("paymentForm").style.display = "none";
                document.getElementById("otpForm").style.display = "block";
                localStorage.setItem('refID', res.data.transaction.refID);
                localStorage.setItem('type', type)
                document.querySelector('.payment-button').disabled = false
                document.querySelector('.payment-button').innerHTML = 'Submit'
            }

        }).catch(e => {
            document.querySelector('.payment-button').disabled = false
        })
        console.log()
    }

    function submitOTP(e) {
        e.preventDefault();
        document.querySelector('.otp-button').disabled = true

        let otp = document.getElementsByName('otp')[0].value
        document.querySelector('.otp-button').innerHTML = 'Submitting'

        axios.post('/pay-otp', {
            otp,
            txnRef: localStorage.getItem('refID'),
            type: localStorage.getItem('type')
        }).then(res => {
            alert('Payment is complete. Thank you.')
            location.reload()
            document.querySelector('.otp-button').disabled = false;
            document.querySelector('.otp-button').innerHTML = 'Submit'

        })

    }

</script>
@endsection
