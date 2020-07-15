<?php
namespace App\Http\Controllers\Operator;

use App\Models\payOrders;
use Baijunyao\Ip\Ip;
use Curl\Curl;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
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
        $data['start_date'] = explode('~',$request->input('activity_time'))[0];
        $data['end_date'] = explode('~',$request->input('activity_time'))[1];
        $data['activity_server'] = $request->input('activity_server');
        $data['activity_ext'] = $request->input('activity_ext');
        $data['activity_name'] = $request->input('activity_name');
        $data['game_name'] = $request->input('game_name');
        $data['activity_status'] = $request->input('activity_status');
        $data['sid'] = $request->input('sid');
        $data['server_id'] = $request->input('server_id');
        $query = DB::connection('mysql_activity');
        $res = $query->table('activity')->insert($data);
        $ret =  [
            'status' => $res ? 200: '-100',
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
        $data['start_date'] = explode('~',$request->input('activity_time'))[0];
        $data['end_date'] = explode('~',$request->input('activity_time'))[1];
        $data['activity_server'] = $request->input('activity_server');
        $data['activity_ext'] = $request->input('activity_ext');
        $data['activity_name'] = $request->input('activity_name');
        $data['game_name'] = $request->input('game_name');
        $data['activity_status'] = $request->input('activity_status');
        $data['sid'] = $request->input('sid');
        $data['server_id'] = $request->input('server_id');
        $query = DB::connection('mysql_activity');

        $res = $query->table('activity')->where(['id'=>$id])->update($data);
        $ret =  [
            'status' => $res ? 200: '-100',
            'message' => $res ? '修改成功':'修改失败',
        ];
        return response()->json($ret);
    }

    public function activity_upd_status(Request $request)
    {
        $id = $request->input('activity');
        $status_map = [1=>2,2=>1];
        $data['activity_status'] = $status_map[$request->input('activity_status')];

        $query = DB::connection('mysql_activity');

        $res = $query->table('activity')->where(['id'=>$id])->update($data);
        $ret =  [
            'status' => $res ? 200: '-100',
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
        list($data['activity_name'],$data['game_name']) = explode('/!/',$request->input('activity_name'));
        $data['award_ext'] = $request->input('award_ext');

        $query = DB::connection('mysql_activity');
        $res = $query->table('award')->insert($data);
        $ret =  [
            'status' => 200,
            'message' => $res ? '添加成功':'添加失败',
        ];

        return response()->json($ret);
    }

    public function award_bulk_add(Request $request)
    {
        $award_bulk_add = $request->input('award_bulk_add');
        if(empty($request->input('award_bulk_add'))) return;
        $i = 0;
        foreach(explode("\n", $award_bulk_add) as $v){
            if(empty($v)) continue;
            $line = explode("\t", $v);
            $for[$i]['game_name'] = $line[0];
            $for[$i]['activity_name'] = $line[1];
            $for[$i]['money'] = $line[2];
            $for[$i]['award'] = $line[3];
            $for[$i]['award_ext'] = $line[4]?$line[4]:'';
            $i ++;
        }
        //批量添加
        $query = DB::connection('mysql_activity');
        $res = $query->table('award')->insert($for);

        $ret =  [
            'status' => 200,
            'message' => $res? '添加成功':'可能有插入失败的',
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
            $res = $query->update('update `award` set award=?,money=?,award_ext=? where id=? and activity_name=? ', [$v[1],$v[0],$v[2],$k,$activity]);
        }

        $ret =  [
            'status' => 200,
            'message' => '更新成功',
        ];
        return response()->json($ret);

    }

    public function award_upds(Request $request)
    {
        $pdata = $request->input();
        $id = $request->input('id');
        unset($pdata['_token']);
        unset($pdata['id']);
        $query = DB::connection('mysql_activity');

        $res = $query->table('award')->where('id',$id)->update($pdata);


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
        //datatables 服务器模式
        $order_column  = $request->input('order');
        $end = $request->input('length')?$request->input('length'):20;

        $db_pay = DB::connection('mysql_activity');
        $res = $db_pay->table('activity.award')
            ->when($activity_name,function ($query) use ($activity_name){
                return $query->where('activity_name','=',$activity_name);
            })
            ->orderby('id','desc')
            ->paginate($end);
        $assign=[
            'data'=>$res,
            'filters'=>[
                'activity_name'=>$activity_name
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
            'status' => $res ? 200: '-100',
            'message' => $res ? '删除成功':'删除失败',
        ];
        return response()->json($ret);
    }

    public function award_edit(Request $request)
    {
        $id = $request->route('id');
        $query = DB::connection('mysql_activity');
        $table = 'award';
        $res = $query->table($table)->where(['id'=>$id])->get()->toarray();
        return view('operator.activity.award_edit')->with(['data'=>$res[0]]);
    }

    public function activity_list(Request $request)
    {
        $game_name = $request->input('game_name');
        $date = $request->input('date');

        $end = $request->input('length')?$request->input('length'):20;

        $table = 'activity';
        $db = DB::connection('mysql_activity');
        $res = $db->table($table)
            ->when($game_name,function ($query) use ($game_name){
                return $query->where('game_name','like','%'.$game_name.'%');
            })
            ->when($date,function ($query) use ($date){
                $start_date = explode('~',$date)[0];
                $end_date = explode('~',$date)[1];
                return $query->where([['start_date','>=',$start_date],['end_date','<=',$end_date]]);
            })
            ->orderby('id','desc')
            ->paginate($end);
        $assign=[
            'data'=>$res,
            'filters'=>[
                'game_name'=>$game_name,
                'date'=>$date,
            ],
        ];

        return view('operator.activity.activitylist',$assign);
    }

    public function activity_del(Request $request)
    {
        $id = $request->input('activity_id');
        $query = DB::connection('mysql_activity');
        $table = 'activity';
        $sql = "delete from {$table} where id={$id}";
        $res = $query->delete($sql);
        $ret =  [
            'status' => 200,
            'message' => $res ? '删除成功':'删除失败',
        ];
        return response()->json($ret);
    }

    public function activity_edit(Request $request)
    {
        $id = $request->route('id');
        $query = DB::connection('mysql_activity');
        $table = 'activity';

        $res = $query->table($table)->where(['id'=>$id])->get()->toarray();
        $start_date = explode('~',$res[0]->activity_time)[0];
        $end_date = explode('~',$res[0]->activity_time)[1];
        return view('operator.activity.activity_edit')->with(['data'=>$res[0],'start_date'=>$start_date,'end_date'=>$end_date]);
    }

    public function user_manage()
    {
        return view('operator.activity.xlczg');
    }

    //添加用户3
    public function add_user(Request $request)
    {
        $uid = $request->input('server_name');
        $realname = $request->input('role_name');
        $role_id = $request->input('role_id');
        $total = $request->input('total');
        $game_name = $request->input('game_name');
        $date = $request->input('date');
        $start_date = explode('~',$date)[0];
        $end_date = explode('~',$date)[1];
        $type = $request->input('type');
        if (!$uid ||!$realname||!$total){
            $ret =  [
                'status' => 300,
                'message' => '请填写必要参数',
            ];
            return response()->json($ret);
        }
        $query = DB::connection('mysql_activity');
        $table = 'recharge_rank';
        if ($type=='consume') $table = 'consume_rank';
        $r = $query->table($table)->where(['role_id'=>$uid,'role_name'=>$realname,'game_name'=>$game_name])->get();
        if (!empty(toArray($r))){
            $ret =  [
                'status' => 300,
                'message' => '用户已存在',
            ];
            return response()->json($ret);
        }
        $data = [
            'server_name'=>$uid,
            'role_name'=>$realname,
            'role_id'=>$role_id,
            'total'=>$total,
            'game_name'=>$game_name,
            'status'=>2,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
        ];


        $res = $query->table($table)->insert($data);

        $ret =  [
            'status' => $res ? 200: '-100',
            'message' => $res ? '添加成功':'添加失败',
        ];
        return response()->json($ret);
    }

    public function user_list(Request $request)
    {
        $only = $request->input('only');
        $consume = $request->input('consume');
        $activity_name = $request->input('activity_name');
        $date = $request->input('date');
        $end = $request->input('length')?$request->input('length'):20;

        $table = 'recharge_rank';
        if ($request->input('consume')) $table = 'consume_rank';
        $db = DB::connection('mysql_activity');
        $res = $db->table($table)
            ->when($activity_name,function ($query) use ($activity_name){
                return $query->where('activity_name','=',$activity_name);
            })
            ->when($date,function ($query) use ($date){
                $start_date = explode('~',$date)[0];
                $end_date = explode('~',$date)[1];
                return $query->where([['start_date','>=',$start_date],['end_date','<=',$end_date]]);
            })
            ->when($only,function ($query) use ($only){
                return $query->where('status','=',2);
            })
            ->orderby('total','desc')
            ->paginate($end);
        $assign=[
            'data'=>$res,
            'filters'=>[
                'date'=>$date,
                'only'=>$only,
                'consume'=>$consume,
            ],
        ];

        return view('operator.activity.userlist',$assign);
    }

    public function user_del(Request $request)
    {
        $id = $request->input('user_id');
        $query = DB::connection('mysql_activity');
        $table = 'recharge_rank';
        if ($request->input('consume') != 'false') $table = 'consume_rank';
        $sql = "delete from {$table} where id={$id}";
        $res = $query->delete($sql);
        $ret =  [
            'status' => 200,
            'message' => $res ? '删除成功':'删除失败',
        ];
        return response()->json($ret);
    }


    public function user_edit(Request $request)
    {
        $id = $request->route('id');
        $query = DB::connection('mysql_activity');
        $table = 'recharge_rank';
        if ($request->input('consume')) $table = 'consume_rank';

        $res = $query->table($table)->where(['id'=>$id])->get()->toarray();
        return view('operator.activity.user_edit')->with(['data'=>$res[0]]);
    }

    public function user_upd(Request $request)
    {
        $id = $request->input('id');
        $data['total'] = $request->input('total');
        $data['game_name'] = $request->input('game_name');
        $data['start_date'] = explode('~',$request->input('activity_time'))[0];
        $data['end_date'] = explode('~',$request->input('activity_time'))[1];
        $query = DB::connection('mysql_activity');
        $table = 'recharge_rank';
        if ($request->input('consume') != 'false') $table = 'consume_rank';
        $res = $query->table($table)->where('id',$id)->update($data);
        $ret =  [
            'status' => $res ? 200: '-100',
            'message' => $res ? '更新成功':'更新失败',
        ];
        return response()->json($ret);
    }

    public function oneKeyFlush(Request $request)
    {
        $db = DB::connection('mysql_activity');
        $table = 'recharge_rank';
        if ($request->input('consume') != 'false') $table = 'consume_rank';
        $res = $db->table($table)->truncate();
        $ret =  [
            'status' => 200,
            'message' => '清空成功',
        ];
        return response()->json($ret);
    }

    //礼包码上传
    public function gift_bag_upload(Request $request)
    {
        global $ret;
        $file = Input::file('csv_file');
        list($activity_name,$game_name) = explode('/!/',$request->input('activity'));
        $query = DB::connection('mysql_activity');

        $res = $query->table('activity')
                ->where(['activity_name'=>$activity_name,'game_name'=>$game_name])
                ->get();

        if (empty($file) || empty($activity_name)){
            $GLOBALS['ret'] =  [
                'status' => 200,
                'message' => '请上传礼包文件或选择活动！',
            ];
            return response()->json($GLOBALS['ret']);
        }

        if($file){
            $realPath = $file->getRealPath();
            $entension =  $file -> getClientOriginalExtension(); //上传文件的后缀.
            $tabl_name = date('YmdHis').mt_rand(100,999);
            $newName = $tabl_name.'.'.'xls';//$entension;
            $path = $file->move(base_path().'/uploads',$newName);
            $cretae_path = base_path().'/uploads/'.$newName;
            $response = response();
            Excel::load($cretae_path, function($reader) use($tabl_name, $res, $query, $response){
                //$data = $reader->all();

                //获取excel的第几张表
                $reader = $reader->getSheet(0);
                //获取表中的数据
                $data = $reader->toArray();
                if ($data[0][0] == null){
                    $GLOBALS['ret'] =  [
                        'status' => 200,
                        'message' =>  '文件为空' ,
                    ];
                    return false;
                }
                $code_arr = [];

                foreach ($data as $v) {
                    $tmp = [
                        'game_name'=>$res[0]->game_name,
                        'activity_name'=>$res[0]->activity_name,
                        'start_date'=>$res[0]->start_date,
                        'end_date'=>$res[0]->end_date,
                        'mark'=> intval($v[1]),
                        'code'=>$v[0],
                        'status'=>1,
                    ];
                    $code_arr[] = $tmp;
                }

                if (!empty($code_arr)){
                    $num = $query->table('gift_code')->insert($code_arr);

                    $GLOBALS['ret'] =  [
                        'status' => 200,
                        'message' => $num ?  '上传成功' : '上传失败',
                    ];
                    return $GLOBALS['ret'];
                }

            });
        }

        return response()->json($ret);
    }

    public function gift_bag_time_change(Request $request)
    {

        $id = $request->input('id');
        $query = DB::connection('mysql_activity');

        $res = $query->table('activity')->where(['id'=>$id])->get();
        $where = [
            'game_name'=>$res[0]->game_name,
            'activity_name'=>$res[0]->activity_name,
        ];
        list($where['start_date'],$where['end_date']) = explode('~',$request->input('date'));

        $data = [
            'start_date'=>$res[0]->start_date,
            'end_date'=>$res[0]->end_date,
        ];
        $res = $query->table('gift_code')->where($where)->update($data);
        $ret =  [
            'status' => $res ? 200: '-100',
            'message' => $res ? '更新成功':'更新失败',
        ];
        return response()->json($ret);

    }
}
