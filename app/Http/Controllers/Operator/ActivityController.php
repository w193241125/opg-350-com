<?php
namespace App\Http\Controllers\Operator;

use App\Models\payOrders;
use Baijunyao\Ip\Ip;
use Curl\Curl;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
class ActivityController extends Controller
{
    public function index()
    {
        $query = DB::connection('mysql_activity')->table('activity');
        $res = $query->get();
        $activity = toArray($res);
        $assign=[
            'activity'=>$activity,
        ];
        return view('operator.activity.index',   $assign);
    }

    public function ajaxGetActivity(Request $request)
    {
        $id = $request->input('activity_id');
        $query = DB::connection('mysql_activity');
        $res = $query->table('activity')->where(['id'=>$id])->get();
        $activity = toArray($res);
        return $activity;
    }

    public function activity_add(Request $request)
    {
        $data['activity_desc'] = $request->input('activity_desc');
        $data['activity_title'] = $request->input('activity_title');
        $data['activity_time'] = $request->input('activity_time');
        $data['activity_server'] = $request->input('activity_server');
        $data['activity_name'] = $request->input('activity_name');
        $data['activity_status'] = $request->input('activity_status');
        $query = DB::connection('mysql_activity');
        $res = $query->table('activity')->insert($data);
        $ret =  [
            'status' => 200,
            'message' => $res ? '添加成功':'添加失败',
        ];
        return response()->json($ret);
    }

    public function activity_upd(Request $request)
    {
        $id = $request->input('activity');
        $data['activity_desc'] = $request->input('activity_desc');
        $data['activity_title'] = $request->input('activity_title');
        $data['activity_time'] = $request->input('activity_time');
        $data['activity_server'] = $request->input('activity_server');
        $data['activity_name'] = $request->input('activity_name');
        $data['activity_status'] = $request->input('activity_status');
        $query = DB::connection('mysql_activity');
        $res = $query->table('activity')->where(['id'=>$id])->update($data);
        $ret =  [
            'status' => 200,
            'message' => $res ? '修改成功':'修改失败',
        ];
        return response()->json($ret);
    }

}
