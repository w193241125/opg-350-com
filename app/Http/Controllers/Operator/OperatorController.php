<?php

namespace App\Http\Controllers\Operator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OperatorController extends Controller
{
    public function queryFailedOrder(Request $request)
    {
        return view('operator.pay.queryFailedOrder');
    }
}
