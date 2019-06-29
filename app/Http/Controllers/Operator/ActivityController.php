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
        $data['activity_ext'] = $request->input('activity_ext');
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
        $data['activity_ext'] = $request->input('activity_ext');
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

    public function activity_award()
    {
        $query = DB::connection('mysql_activity')->table('activity');
        $res = $query->get();
        $activity = toArray($res);
        $assign=[
            'activity'=>$activity,
        ];
        return view('operator.activity.award',   $assign);
    }

    public function award_add(Request $request)
    {
        $data['money'] = $request->input('money');
        $data['award'] = $request->input('award');
        $data['activity_name'] = $request->input('activity_name');
        $query = DB::connection('mysql_activity');
        $res = $query->table('award')->insert($data);
        $ret =  [
            'status' => 200,
            'message' => $res ? '添加成功':'添加失败',
        ];
        return response()->json($ret);
    }

    public function award_upd(Request $request)
    {
        $pdata = $request->input();
        unset($pdata['_token']);
        $query = DB::connection('mysql_activity');
        foreach ($pdata as $k=>$v) {
            if ($k=='activity'){$activity = $v;continue;}
            $res = $query->update('update `award` set award=? where money=? and activity_name=? ', [$v,$k,$activity]);
        }

        $ret =  [
            'status' => 200,
            'message' => '更新成功',
        ];
        return response()->json($ret);

    }

    public function ajaxGetAward(Request $request)
    {
        $name = $request->input('activity_name');
        $query = DB::connection('mysql_activity');
        $res = $query->table('award')->where(['activity_name'=>$name])->get();
        $activity = toArray($res);
        return $activity;
    }

    public function award_list(Request $request)
    {
        $activity_name = $request->input('activity_name');
        $pay_channel = $request->input('pay_channel');

        //datatables 服务器模式
        $order_column  = $request->input('order');
        $end = $request->input('length')?$request->input('length'):20;

        $db_pay = DB::connection('mysql_activity');
        $res = $db_pay->table('activity.award')
            ->when($activity_name,function ($query) use ($activity_name){
                return $query->where('activity_name','=',$activity_name);
            })
            ->paginate($end);
        $assign=[
            'data'=>$res,
            'filters'=>[
            ],
        ];

        return view('operator.activity.awardlist',$assign);
    }

    public function award_del(Request $request)
    {
        $id = $request->input('award_id');
        $query = DB::connection('mysql_activity');
        $sql = "delete from activity.award where id={$id}";
        $res = $query->delete($sql);
        $ret =  [
            'status' => 200,
            'message' => $res ? '删除成功':'删除失败',
        ];
        return response()->json($ret);
    }
}
