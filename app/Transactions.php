<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $fillable = ['client_first_name', 'client_last_name', 'email', 'amount', 'type', 'status', 'refID', 'user_id'];
}
