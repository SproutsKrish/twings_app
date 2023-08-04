<?php

namespace App\Http\Controllers\License;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;


use App\Models\LicenseTransaction;

class TransactionHeadController extends BaseController
{
    public function index()
    {
        $transaction_heads = LicenseTransaction::all();

        if ($transaction_heads->isEmpty()) {
            return $this->sendError('No License Transaction Heads Found');
        }

        return $this->sendSuccess($transaction_heads);
    }
}
