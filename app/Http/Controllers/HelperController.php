<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HelperController extends Controller
{
    public function geiUserMD5_IMEI()
    {
        $use_imei = DB::table('user_imei')->select('imei')->get();
        $cellData[0] = ['imei'];
        foreach ($use_imei as $item) {
            $imei = md5(substr($item->imei, strpos($item->imei, '-')+1)).'\n';
            array_push($cellData,$imei);
        }

        Excel::create('imei表',function($excel) use ($cellData){
            $excel->sheet('order', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xlsx');
    }
}
