<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserPubKeyModel extends Model
{
    public  $table = 'user_pubkey';
    protected $primarykey='uid';
}
