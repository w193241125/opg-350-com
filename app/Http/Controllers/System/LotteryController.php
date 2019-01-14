<?php

namespace App\Http\Controllers\System;

use Curl\Curl;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class LotteryController extends Controller
{

    public function index()
    {
        $query = DB::connection('mysql_lucky');
        $res = $query->table('lucky_turn')->get();
        $turns = toArray($res);
        return view('system.lottery',['turns'=>$turns]);
    }

    //获取所有抽奖等级
    public function ajaxGetTurn(Request $request)
    {
        $query = DB::connection('mysql_lucky');
        $res = $query->table('lucky_turn')->get();
        $turns = toArray($res);
        return $turns;
    }

    //检查轮数是否存在
    public function ajaxGetTurns(Request $request)
    {
        $turn_id = $request->input('turn_id');
        $query = DB::connection('mysql_lucky');
        $res = $query->table('lucky_turn')->where(['turn_id'=>$turn_id])->get();
        $turns = toArray($res);
        $re = $query->table('lucky_config')->get();
        $turns[0]['turn_pre_num'] = $re[0]->turn_pre_num;
        return $turns;
    }

    //检查额外轮数标识是否已经存在
    public function ajaxGetMarkIfExist(Request $request)
    {
        $turn_name = $request->input('turn_name');
        $query = DB::connection('mysql_lucky');
        $res = $query->table('lucky_turn')->where(['turn_name'=>$turn_name])->get();
        $turns = toArray($res);
        return $turns;
    }

    //检查用户id是否已经存在
    public function ajaxcheckUid(Request $request)
    {
        $uid = $request->input('uid');
        $query = DB::connection('mysql_lucky');
        $res = $query->table('lucky_user')->where(['id'=>$uid])->get();
        $turns = toArray($res);
        return $turns;
    }

    //抽奖配置
    public function lotteryone(Request $request)
    {
        $turn_id = $request->input('turn');
        $turn_pre_num = $request->input('pre_num');
        $turn_total_number = $request->input('total');
        if (!$turn_id ||!$turn_pre_num || !$turn_total_number){
            $ret =  [
                'status' => 300,
                'message' => '请选择抽奖等级,填写每次抽奖人数与总数',
            ];
            return response()->json($ret);
        }

        $data = [
            'turn_id'=>$turn_id,
            'turn_pre_num'=>$turn_pre_num,
        ];
        $query = DB::connection('mysql_lucky');
        $res = $query->table('lucky_config')->update($data);
        $lucky_turn = [
            'turn_total_number'=>$turn_total_number
        ];
        $re =  $query->table('lucky_turn')->where(['turn_id'=>$turn_id])->update($lucky_turn);

        $ret =  [
            'status' => 200,
            'message' => '修改成功',
        ];
        return response()->json($ret);
    }

    //加抽2
    public function lotterytwo(Request $request)
    {
        $turn_name = $request->input('turn_name');
        $turn_pre_num = $request->input('pre_nums');
        $turn_total_number = $request->input('totals');
        $man = $request->input('man');
        $mutex = $request->input('mutex');
        $mutex_turn = $request->input('mutex_turn');
        $woman = $request->input('peoples');
        if (!$turn_name ||!$turn_pre_num || !$turn_total_number ||!$man){
            $ret =  [
                'status' => 300,
                'message' => '请填写标识,每轮抽奖数，总数',
            ];
            return response()->json($ret);
        }
        $query = DB::connection('mysql_lucky');
        //验证标识是否已经存在
        $tid = $query->table('lucky_turn')->where(['turn_name'=>$turn_name])->get();
        if (!empty(toArray($tid))){
            $ret =  [
                'status' => 300,
                'message' => '标识已存在',
            ];
            return response()->json($ret);
        }

        //检验是否与其它奖项互斥
        if ($mutex==100){
            //不互斥
        }elseif($mutex==200){
            //互斥
            $res = $query->table('lucky_record')->select(['user_id'])->whereIn('turn_id',$mutex_turn)->get();
            if (!empty($res)){
                foreach ($res as $re) {
                    $turn_id[] = $re->user_id;
                }
            }
        }else{
            $ret =  [
                'status' => 300,
                'message' => '未知错误',
            ];
            return response()->json($ret);
        }

        //添加奖池
        if ($man==100){//所有
            $res = $query->table('lucky_user')
                ->select(['id','level'])
                ->when($turn_id,function ($query) use ($turn_id){
                    return $query->whereNotIn('id',$turn_id);
                })
                ->get();
        }elseif($man==200){//部分
            if (empty($woman)){
                $ret =  [
                    'status' => 300,
                    'message' => '请选择参抽人员',
                ];
                return response()->json($ret);
            }
            $res = $query->table('lucky_user')
                ->select(['id','level'])
                ->when($turn_id,function ($query) use ($turn_id){
                    return $query->whereNotIn('id',$turn_id);
                })
                ->whereIn('level',$woman)
                ->get();
        }else{
            $ret =  [
                'status' => 300,
                'message' => '未知错误',
            ];
            return response()->json($ret);
        }

        foreach ($res as $re) {
            $tmp[$re->id] = $re->id;
            $all_user[$re->id] = $re->level;
        }
        if (count($tmp)<$turn_total_number){
            $ret =  [
                'status' => 300,
                'message' => '参加抽奖的人员数量少于抽奖总数_'.count($tmp),
            ];
            return response()->json($ret);
        }
        //添加抽奖奖项
        $lucky_turn = [
            'turn_name'=>$turn_name,
            'turn_total_number'=>$turn_total_number
        ];
        $re_turnid =  $query->table('lucky_turn')->insertGetId($lucky_turn);
        
        $pool = array_rand($tmp,$turn_total_number);
        if (is_array($pool)){
            foreach ($pool as $p) {
                $arr[] = $tmp[$p];
                $data['user_id'] = $tmp[$p];
                $data['level'] = $all_user[$tmp[$p]];
                $data['turn_id'] = $re_turnid;
                $data['rank'] = $re_turnid;
                $data['rank_mark'] = $turn_name;
                $query->table('lucky_record')->insert($data);
            }
        }else{
            $data['user_id'] = $tmp[$pool];
            $data['level'] = $all_user[$tmp[$pool]];
            $data['turn_id'] = $re_turnid;
            $data['rank'] = $re_turnid;
            $data['rank_mark'] = $turn_name;
            $query->table('lucky_record')->insert($data);
        }

        $redis = Redis::connection();
        $redis->sadd('turn_'.$re_turnid,$arr);

        //更新当前抽奖配置
        $data = [
            'turn_id'=>$re_turnid,
            'turn_pre_num'=>$turn_pre_num,
        ];
        $res =$query->table('lucky_config')->update($data);
        

        $ret =  [
            'status' => 200,
            'message' => $res ? '添加成功':'添加失败',
        ];
        return response()->json($ret);
    }

    //添加用户3
    public function lotterythree(Request $request)
    {
        $uid = $request->input('username');
        $realname = $request->input('trueName');
        $level = $request->input('level');

        if (!$uid ||!$realname||!$level){
            $ret =  [
                'status' => 300,
                'message' => '请填写序号或姓名,用户等级',
            ];
            return response()->json($ret);
        }
        $query = DB::connection('mysql_lucky');
        $r = $query->table('lucky_user')->where(['id'=>$uid])->get();
        if (!empty(toArray($r))){
            $ret =  [
                'status' => 300,
                'message' => '用户已存在',
            ];
            return response()->json($ret);
        }
        $data = [
            'id'=>$uid,
            'real_name'=>$realname,
            'level'=>$level,
        ];


        $res = $query->table('lucky_user')->insert($data);

        $ret =  [
            'status' => 200,
            'message' => $res ? '添加成功':'添加失败',
        ];
        return response()->json($ret);
    }

    //展示用户
    public function user()
    {
        $query = DB::connection('mysql_lucky');
        $user = $query->table('lucky_user')->get();
        return view('system.lotteryuser',['user'=>$user]);
    }
    
    //一键清空
    public function oneKeyFlush(Curl $curl)
    {
        $key = 'yW3HbG58mxoToBIN';
        $data['time'] = time();
        $data['sign'] = md5($key.$data['time']);

        $res = $curl->post('http://lucky.350.com/api/flush_all.php',$data);

//        $query = DB::connection('mysql_lucky');
//        $res = $query->table('lucky_turn')->select('turn_id')->get();
//        $redis = Redis::connection();
//        foreach ($res as $re) {
//            $redis_keys[] = 'turn_'.$re->turn_id;
//        }
//        $res = $redis->del($redis_keys);
//        $query->table('lucky_turn')->where('turn_id','>',3)->delete();
//        $query->table('lucky_record')->where('turn_id','>',3)->delete();
        $ret =  [
            'status' => 200,
            'message' => $res ? '清空成功'.$res:'清空失败'.$res,
        ];
        return response()->json($ret);
    }
    
    //生成奖池
    public function setPool(Curl $curl)
    {
        $key = 'oKMuiE8aAiqzeWpl';
        $data['time'] = time();
        $data['sign'] = md5($key.$data['time']);

        $res = $curl->post('http://lucky.350.com/api/generate_rank.php',$data);

        $ret =  [
            'status' => 200,
            'message' => $res ? '成功'.$res:'失败'.$res,
        ];
        return response()->json($ret);
    }

    //展示中奖用户
    public function lotterywinner()
    {
        $query = DB::connection('mysql_lucky');
        $res = $query->table('lucky_user')->get();
        foreach ($res as $v) {
            $user[$v->id] = $v->real_name;
        }
        $lotterywinner = $query->table('lucky_record')->get();
        return view('system.lotterywinner',['user'=>$user,'lotterywinner'=>$lotterywinner]);
    }

}
