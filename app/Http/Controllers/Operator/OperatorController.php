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
class OperatorController extends Controller
{
    public  $payGoldType = array(
        "100" => "测试充值", "101" => "人工充值", "110" => "苹果官方丢单补发", "102" => "帐号充错补发", "103" => "帐号充错游戏补发",
        "104" => "帐号充错服务器补发", "105" => "充值成功,游戏币未发", "106" => "充值成功,但是游戏币发放查询没记录", "107" => "充值成功,帐号游戏服全都出错,游戏币发放查询没记录",
        "108" => "游戏活动奖励发放游戏币（算收入）", "109" => "支付成功,订单状态显示支付失败(需截图证明)",
    );

    /**
     * 充值失败订单扫描
     * @param Request $request
     * @param payOrders $payOrders
     * @param Curl $curl
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function queryFailedOrder(Request $request, payOrders $payOrders,Curl $curl)
    {
        $plat_id = 1;
        $plat_arr = $payOrders->getPlatsGamesServers();
        $games_arr = $payOrders->getGames2($plat_id);

//        $sdate = $request->sdate ? $request->sdate : date("Y-m-d");
//        $sdate = $request->sdate ? $request->sdate : '2018-10-08';
//        $edate = $request->edate ? $request->edate : $sdate;
        $sdate     =  $request->input('date') ? substr($request->input('date'),0,10) : date("Y-m-d", time());
        $edate     =  $request->input('date') ? substr($request->input('date'),11,10) : date("Y-m-d", time());
        if ($request->do == 'bf'){

        }elseif ($request->do == 'orders'){

        }else{
            $payChannel = Cache::get('payChannel'); //获取支付渠道数组
            $plat_arr    = $payOrders->getPlatsGamesServers(1);
            $games_arr   = $payOrders->getGames2($plat_id);
            $pay_url = getenv('PAY_URL');
            //获取发放游戏币失败的订单
            $post_arr          = array();
            $post_arr['sdate'] = $sdate;
            $post_arr['edate'] = $edate;
            $post_arr['action'] = 'Scanlist';
            $post_arr['opt'] = 'queryFailOrder';
            $post_arr['admin_username'] = 'jishubu';
            $post_arr['time'] = time();
            $post_arr['flag'] = md5(md5($post_arr['time'].getenv('POST_KEY')).getenv('POST_KEY'));

            $result = $curl->post($pay_url,$post_arr);

            if ($result){
                $res_arr = json_decode($result,true);
                foreach ($res_arr as $key => $val) {
                    $returnMsg                     = unserialize($games_arr[ $val['game_id'] ]['result_code']);
                    $res_arr[ $key ]['user_ip']     = long2ip(intval($val['user_ip']));
                    $res_arr[ $key ]['return_msg']  = $returnMsg[ $val['back_result'] ];
                    $res_arr[ $key ]['game_byname'] = $games_arr[ $val['game_id'] ]['game_byname'];
                    //$result[ $key ]['pay_channel'] = $payChannel[ $val['pay_channel'] ]['remark'];
                    $res_arr[ $key ]['pay_channel'] = '支付宝';//todo

                    $res_arr[ $key ]['sign']        = md5($val['orderid'] . $val['user_name'] . $val['succ'] . $val['money'] . $val['pay_gold'] . $val['game_id'] . $val['server_id'] . '0000');
                }
            }
        }
        if (!$res_arr){
            $returns = [
                'status' => 300,
                'message' => '查无订单',
            ];
//            return response()->json($returns);
        }
        $assign = [
            'plat_arr'=>$plat_arr,
            'games_arr'=>$games_arr,
            'failed_list'=>$res_arr,
            'filters'=>[
                'date'=>$request->date,
                'user_name'=>$request->user_name,
            ],
        ];
        return view('operator.pay.queryFailedOrder',$assign);
    }

    /**
     * 补发元宝
     * @param Request $request
     * @param Curl $curl
     * @return \Illuminate\Http\JsonResponse
     */
    public function bf(Request $request,Curl $curl)
    {

        $user_name = $request->user_name;
        $plat_id   = intval($request->plat_id);
        $game_id   = intval($request->game_id);
        $server_id = intval($request->server_id);
        $gold      = intval($request->pay_gold);
        $money     = $request->money;
        $orderid   = $request->orderid;
        $succ      = intval($request->succ);
        $pay_type  = 105;
        $remark    = $_SESSION['uName'] . "为玩家自动补，后台自动扫描方式。";

        $game_byname = $request->game_byname;
        $sign        = $request->sign;
        if ($succ != 1) {
            $returns = [
                'status' => 300,
                'message' => '该订单没有支付成功，请跟支付渠道确认再进行补发！',
            ];
            return response()->json($returns);
        }
        if ($sign != md5($orderid . $user_name . $succ . $money . $gold . $game_id . $server_id . '0000')) {
            $returns = [
                'status' => 300,
                'message' => '非法操作!',
            ];
            return response()->json($returns);
        }


        //查询订单的支付状态和游戏币是否发放成功
        $post_arr = array();
        $post_arr['search_type'] = 1;
        $post_arr['game_id']     = $game_id;
        $post_arr['orderid']     = $orderid;
        $post_arr['orderid']     = $orderid;
        $post_arr['action'] = 'Payquery';
        $post_arr['opt'] = 'bf';
        $post_arr['admin_username'] = 'jishubu';
        $post_arr['time'] = time();
        $post_arr['flag'] = md5(md5($post_arr['time'].getenv('POST_KEY')).getenv('POST_KEY'));

        $pay_info = $curl->post(getenv('PAY_URL'),$post_arr);
        $pay_info = json_decode($pay_info,true);
        if (trim($pay_info[0]['succ']) != 1) {
            $returns = [
                'status' => 300,
                'message' => '该订单没有支付成功，请跟支付渠道确认再进行补发！',
            ];
            return response()->json($returns);
        }

        if ($server_id == 0) { //苹果漏单处理
            $remark    = $_SESSION['uName'] . "为玩家自动补，后台自动扫描方式。(苹果漏单)";
            $server_id = $this->check_miss_serverid($user_name, $plat_id, $game_id, $server_id);
            $pay_type  = 104;
            if ($gold == 0) $gold = $money * 10;
        }


        //游戏币补发
        $result = $this->sendGold($user_name, $plat_id, $game_id, $server_id, $gold, $money, $pay_type, $remark, $orderid);

        return response()->json($result);
    }

    /**
     * 发放游戏币动作 私有方法
     * @params orderid 人工充值  游戏测试充值时不需要传
     * @params bank_date/bank_name 人工充值时必须传充值时间和充值银行
     */
    private function sendGold($user_name, $plat_id, $game_id, $server_id, $b_num, $money, $pay_type, $remark, $orderid = '', $bank_date = '', $bank_name = '', $id = '', $role_id = '', $role_name = '') {
        $curl = new Curl();
        if ($plat_id == 2) $plat_id = 1;
        $money = (float)$money;
        if (in_array($pay_type, array(100, 101, 108)) && $id) {//人工充值没订单号时，禁止连续多次提交
            if ($_SESSION[ 'sendGold_' . $pay_type ][ $id ] && (time() - $_SESSION[ 'sendGold_' . $pay_type ][ $id ]) < 20) {
                $returns = [
                    'status' => 200,
                    'message' => '该订单请求已发送啦，请耐心等候20秒，刷新页面看看是否已充值成功！',
                ];
                return response()->json($returns);
            } else {
                $_SESSION[ 'sendGold_' . $pay_type ][ $id ] = time();
                if ($pay_type == 101) {
                    $table = getenv('GOLDREISSUE');
                } else {
                    $table = getenv('GOLDTEST');
                }
                $orderInfo = DB::table($table)->where([id,'=',$id])->get()->toArray();//todo
                if ($orderInfo[0]->state == 2) {
                    $returns = [
                        'status' => 200,
                        'message' => '该订单请求已充值成功的啦!',
                    ];
                    return response()->json($returns);
                }
            }
        }

        if ($pay_type == 100 && $money > 1) {
            $returns = [
                'status' => 300,
                'message' => '测试游戏金额不能大于1RMB!',
            ];
            return response()->json($returns);
        }

        if ($pay_type == 108 && $money > 10000) {
            $returns = [
                'status' => 300,
                'message' => '活动奖励金额不能大于10000RMB!',
            ];
            return response()->json($returns);
        }


        if (!$plat_id || !$game_id || $user_name == '' || $b_num < 1 || $money == '' || !$pay_type || $remark == '') {
            $returns = [
                'status' => 300,
                'message' => '充值帐号，游戏币数量，订单金额，充值说明，游戏，服务器不能为空!',
            ];
            return response()->json($returns);
        }

        $all_game         = $this->getPlatsGamesServers(0);
        $plat_name   = $all_game['plats'][ $plat_id ]['plat_name'];
        $game_name   = $all_game['games'][ $plat_id ][ $game_id ]['name'];
        $server_name = $all_game['servers'][ $plat_id ][ $game_id ][ $server_id ]['name'];
        $type_name   = $this->payGoldType[ $pay_type ];

        //发放游戏币
        //获取加密key 加密
        $Key_2918 = getenv('PAY_API_KEY_SENDGOLD');
        $time     = time();
        $pay_ip   = GetIP();
        $flag     = md5($time . $Key_2918 . $user_name . $game_id . $server_id . $pay_ip);

        $post_arr                   = array();
        $post_arr['admin_username'] = $_SESSION['uName'];
        $post_arr['user_name']      = trim($user_name);//
        $post_arr['time']           = $time;
        $post_arr['game_id']        = $game_id;//
        $post_arr['server_id']      = $server_id;//
        $post_arr['b_num']          = $b_num;//
        $post_arr['money']          = $money;//
        $post_arr['pay_type']       = $pay_type;//
        $post_arr['flag']           = $flag;
        $post_arr['orderid']        = $orderid;//
        $post_arr['pay_ip']         = $pay_ip;
        $post_arr['remark']         = $remark;//
        $post_arr['bank_date']      = $bank_date;//
        $post_arr['bank_name']      = $bank_name;//
        $post_arr['role_id']        = $role_id;//
        $post_arr['role_name']      = $role_name;//
        $commMsg = "游戏币补发：{$user_name}|{$plat_name}|{$game_name}|{$server_id}服|{$money} RMB|{$b_num} 游戏币|{$type_name}|";

        $url = getenv('SEND_GOLD_URL');
        $contents = $curl->post($url,$post_arr);
        $status = 300;

        if ($contents == "1") {
            $status  = 200;
            $log_msg = "{$commMsg}成功({$contents})|{$remark}";
            $showtip = '充值成功';
        } elseif ($contents == "5") {
            $log_msg = "{$commMsg}失败({$contents})，需要填写订单号|{$remark}";
            $showtip = '非测试/人工充值/奖励发放 需要填写订单号，或此笔订单已成功';
        } elseif ($contents == "0") {
            $log_msg = "{$commMsg}失败({$contents})|{$remark}";
            $showtip = '充值失败';
        } elseif ($contents == "6") {
            $log_msg = "{$commMsg}失败({$contents})，订单号不存在|{$remark}";
            $showtip = '充值失败，由于订单号不存在';
        } else {
            $log_msg = "{$commMsg}失败({$contents})|{$remark}";
            $showtip = '充值失败';
        }

        $this->setLog($log_msg);//记录操作日志


        $returns = [
            'status' => $status,
            'message' => $showtip . $contents,
        ];
        return $returns;

    }

    private function check_miss_serverid(Curl $curl, $user_name, $plat_id, $game_id, $server_id) {
        $Key_2918 = getenv('PAY_API_KEY_SENDGOLD');
        $time     = time();
        $pay_ip   = GetIP();
        $flag     = md5($time . $Key_2918 . $user_name . $game_id . $server_id . $pay_ip);

        $post_arr              = array();
        $post_arr['user_name'] = $user_name;
        $post_arr['game_id']   = $game_id;
        $post_arr['server_id'] = $server_id;
        $post_arr['time']      = $time;
        $post_arr['pay_ip']    = $pay_ip;
        $post_arr['flat']      = $flag;
        $url                   = getenv('SYNC_PRE_SERVER');
        $result                = $curl->post($url, $post_arr);
        return $result;
    }

    /**
     * 数据按日统计
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function data_statistics_day(Request $request)
    {
        $game_list = $this->getPlatsGamesServers(2, 1, 0, 0, 0, 0, 0, 0, 2);
        $game_sort_list = $this->getGameSorts();
        $pay_table = 'sy_center.sy_game_total_day';
        $sdate     =  $request->input('date') ? substr($request->input('date'),0,10) : date("Y-m-d", time() - 86400 * 6);
        $edate     =  $request->input('date') ? substr($request->input('date'),11,10) : date("Y-m-d", time());
        $where = [
            ['tdate','>=',"$sdate"],
            ['tdate','<=',"$edate"],
        ];
        $agent_id = $request->input('agent_id');
        $site_id = $request->input('site_id');
        $game_id = $request->input('game_id');

        $query = DB::connection('mysql_opgroup')->table($pay_table);
        $columns = ' tdate,sum(reg_total) as reg_total,sum(login_total) as login_total,sum(active_total) as active_total,sum(pay_total) as pay_total,sum(pay_total) as pay_amount,sum(pay_num) as pay_num,sum(old_tdate) as old_login_total,sum(old_login_next) as old_login_next,sum(old_pay_total) as old_pay_total,sum(old_pay_total) as old_pay_amount,sum(old_pay_num) as old_pay_num,sum(pay_tdate) as pay_tdate,sum(pay_num_tdate) as pay_num_tdate ';

        $res = $query
            ->select(DB::raw($columns))
            ->where($where)->groupBy(['tdate'])
            ->when($agent_id,function ($query) use ($agent_id){
                return $query->where('agent_id','=',$agent_id);
            })
            ->when($site_id,function ($query) use ($site_id){
                return $query->where('site_id','=',$site_id);
            })
            ->when($game_id,function ($query) use ($game_id){
                return $query->whereIn('game_id',$game_id);
            })
            ->orderBy('tdate')
            ->get();

        $res = json_decode(json_encode($res),true);
        $ress = $this->datasum_do($res);
        $assign=[
            'data'=>$ress['res'],
            'data_sum'=>$ress['sum'],
            'data_user_pay'=>$ress['user_pay'],
            'filters'=>[
                'agent_id'=>$request->agent_id,
                'site_id'=>$request->site_id,
                'date'=>$request->date,
                'game_id'=>$request->game_id?$request->game_id:array(),
            ],
            'game_list'=>$game_list,
            'game_sort_list'=>$game_sort_list,
        ];

        return view('operator.data.data_statistics_day',$assign);
    }

    //数据处理 合并 求和 百分比
    private function datasum_do($res){
        $result_sum = $user_pay = array();
        $res_pay_arr = array();
        foreach ($res as $key => $val){
            $res_pay_arr[$key]['pay_tdate'] = $val['pay_tdate'];
        }

        foreach($res as $k=>$v){
            $res[$k]['reg'] = $v['reg_total']+$v['old_login_total'];
            $res[$k]['login'] = $v['login_total']+$v['old_login_next'];
            $res[$k]['all_remain'] = $res[$k]['reg']>0 ? round($res[$k]['login']/$res[$k]['reg'],4) : 0; // todo 计算规则
            $res[$k]['pay'] = $v['pay_tdate'];
            $res[$k]['old_pay_total'] = $v['pay_tdate'] - $v['pay_total'];
            $res[$k]['amount'] = $v['pay_tdate'];
            $res[$k]['old_pay_amount'] = $v['pay_tdate'] - $v['pay_amount'];
            $res[$k]['pay_u'] = $v['pay_num']+$v['old_pay_num'];
            $res[$k]['arpu'] = $res[$k]['pay_u']>0? round($res[$k]['pay']/$res[$k]['pay_u'],2) : 0;
            $res[$k]['pay_rate'] = $res[$k]['reg']>0? round($res[$k]['pay_u']/$res[$k]['reg'],4) : 0;

            $res[$k]['reg_remain'] = $v['reg_total']>0 ? round($v['active_total']/$v['reg_total'],4) : 0;
            $res[$k]['reg_arpu'] = $v['pay_num']>0 ? round($v['pay_total']/$v['pay_num'],2) : 0;
            $res[$k]['reg_pay_rate'] = $v['pay_num']>0 ? round($v['pay_num']/$v['reg_total'],4) : 0;
            $res[$k]['reg_arpus'] = $v['pay_total']>0 ? round($v['pay_total']/$v['reg_total'],2) : 0;

            //计算环比增长
            $res[$k]['huan_pay_rate'] = $res_pay_arr[$k-1]['pay_tdate']>0?round(($res[$k]['pay'] - $res_pay_arr[$k-1]['pay_tdate'])/$res_pay_arr[$k-1]['pay_tdate'],4):0;
            $old_login_off = $v['old_login_total'] - $v['old_login_next'];
            // $res[$k]['old_off'] = $v['old_login_total']>0 && $old_login_off>0 ? round($old_login_off/$v['old_login_total'],2) : 0;  // 与合并部分计算方式统一
            $res[$k]['old_off'] = $v['old_login_total']>0 ? round($old_login_off/$v['old_login_total'],4) : 0;
            $res[$k]['old_arpu'] = $v['old_pay_num']>0 ? round($v['old_pay_total']/$v['old_pay_num'],2) : 0;
            $res[$k]['old_pay_rate'] = $v['old_login_total']>0 ? round($v['old_pay_num']/$v['old_login_total'],4) : 0;

            //合并
            $result_sum['reg_total'] += $v['reg_total'];
            $result_sum['login_total'] += $v['login_total'];
            $result_sum['pay_total'] += $v['pay_total'];
            $result_sum['pay_amount'] += $res[$k]['pay_amount'];
            $result_sum['pay_num'] += $v['pay_num'];

            $result_sum['old_login_total'] += $v['old_login_total'];
            $result_sum['old_login_next'] += $v['old_login_next'];
            $result_sum['old_login_off'] += $old_login_off;
            $result_sum['old_pay_total'] += $res[$k]['old_pay_total'];
            $result_sum['old_pay_amount'] += $res[$k]['old_pay_amount'];
            $result_sum['old_pay_num'] += $v['old_pay_num'];

            $user_pay['new'][$v['tdate']] += $v['pay_total']+0;
            $user_pay['old'][$v['tdate']] += $v['pay_tdate'] - $v['pay_total']+0;
            $user_pay['all'][$v['tdate']] += ($v['pay_tdate']+0);
        }

        $result_sum['reg_remain'] = $result_sum['reg_total']>0 ? round($result_sum['active_total']/$result_sum['reg_total'],4):0;
        $result_sum['arpu'] = $result_sum['pay_num']>0 ? round($result_sum['pay_total']/$result_sum['pay_num'],2):0;
        $result_sum['arpus'] = $result_sum['pay_total']>0 ? round($result_sum['pay_total']/$result_sum['reg_total'],2):0;
        $result_sum['pay_rate'] = $result_sum['reg_total']>0 ? round($result_sum['pay_num']/$result_sum['reg_total'],4):0;
        $result_sum['old_off'] = $result_sum['old_login_total']>0 ? round($old_login_off/$result_sum['old_login_total'],4):0;
        $result_sum['old_arpu'] = $result_sum['old_pay_num']>0 ? round($result_sum['old_pay_total']/$result_sum['old_pay_num'],2):0;
        $result_sum['old_pay_rate'] = $result_sum['old_login_total']>0 ? round($result_sum['old_pay_num']/$result_sum['old_login_total'],4):0;

        $result_sum['all_user'] = $result_sum['reg_total']+$result_sum['old_login_total'];
        $tmp = $result_sum['reg_total']+$result_sum['old_login_total'];
        $result_sum['all_remain'] = $tmp>0 ? round(($result_sum['active_total']+$result_sum['old_login_next'])/$tmp,4) : 0;
        $result_sum['all_pay'] = $result_sum['pay_total']+$result_sum['old_pay_total'];
        $result_sum['all_amount'] = $result_sum['pay_amount']+$result_sum['old_pay_amount'];
        $result_sum['all_pay_num'] = $result_sum['pay_num']+$result_sum['old_pay_num'];
        $result_sum['all_arpu'] = $result_sum['all_pay_num']>0? round($result_sum['all_pay']/$result_sum['all_pay_num'],2):0;
        $result_sum['all_pay_rate'] = $result_sum['all_user']>0? round($result_sum['all_pay_num']/$result_sum['all_user'],4):0;

        return array('sum'=>$result_sum,'res'=>$res,'user_pay'=>$user_pay);
    }

    public function incomeBABG(Request $request,Curl $curl)
    {
        $game_list = $this->getPlatsGamesServers(2, 1);
        $game_sort_list = $this->getGameSorts();
        $payChannel = $this->getPayChannel();

        $sdate     =  $request->input('date') ? substr($request->input('date'),0,10) : date("Y-m-d", time() - 86400 * 7);
        $edate     =  $request->input('date') ? substr($request->input('date'),11,10) : date("Y-m-d", time() - 86400);
        $moneytype = intval($request->input('moneytype')); //充值类型
        $game_id = $request->input('game_id');
        $plat_id = $request->input('plat_id');
        //处理game_id 为字串形式（如：1,2,3）
        if (!empty($game_id)){
            $game_id = implode(',', $game_id);
        }
        $post_arr              = array();
        $post_arr['game_id']   = $game_id;
        $post_arr['moneytype'] = $moneytype;
        $post_arr['sdate']     = $sdate;
        $post_arr['edate']     = $edate;
        $post_arr['plat_id']   = 1;

        $post_arr['action'] = 'PayMoneyList';
        $post_arr['opt'] = 'IncomeBABG';
        $post_arr['admin_username'] = 'jishubu';
        $post_arr['time'] = time();
        $post_arr['flag'] = md5(md5($post_arr['time'].getenv('POST_KEY')).getenv('POST_KEY'));
        $url = getenv('PAY_URL');
        $data = $curl->post($url,$post_arr);
        $data = json_decode($data,true);

        //记录表头 渠道的顺序
        $tablehead = '';
        $i        = $j = 1;
        $remember = $result = $channelTotal = array();
        foreach ($payChannel as $k => $v) {
            $tablehead      .= '<td><b>' . $v['remark'] . '</b></td>';
            $remember[ $i ] = $v['id'];
            $i++;
        }
        //将数据按照表头的支付渠道的顺序 调整为对应的
        foreach ($data as $k2 => $v2) {
            $j = 1;
            for ($j = 1; $j < $i; $j++) {
                $result[ $k2 ][ $remember[ $j ] ] = intval($v2[ $remember[ $j ] ]);
            }
        }

        //统计各个游戏在各个支付渠道 的充值总数
        foreach ($result as $k3 => $v3) {
            $result[ $k3 ] = array_merge(array('total'=>array_sum($v3)),$result[ $k3 ]);//将各渠道充值总数放入数组开头
        }
        //统计各个支付渠道在各个游戏 的充值总数
        foreach ($result as $ck => $cv) {
            foreach ($cv as $ckk => $cvv) {
                $channelTotal[ $ckk ] += $cvv;
            }
        }


        $where = [
            ['tdate','>=',"$sdate"],
            ['tdate','<=',"$edate"],
        ];

        $assign=[
            'payChannel'=>$payChannel,
            'result'=>$result,
            'channelTotal'=>$channelTotal,
            'filters'=>[
                'plant_id'=>$request->plant_id,
                'moneytype'=>$request->moneytype,
                'date'=>$request->date,
                'game_id'=>$request->game_id?$request->game_id:array(),
            ],
            'game_list'=>$game_list,
            'game_sort_list'=>$game_sort_list,
        ];

        return view('operator.data.incomeBABG',$assign);
    }

    //充值支付列表
    public function payListQuery(Request $request)
    {
        $game_list = $this->getPlatsGamesServers(2, 1, 0, 0, 0, 0, 0, 0, 2);
        $game_sort_list = $this->getGameSorts();
        $game_sort = array();
        foreach ($game_sort_list as $ke =>$va){
            $game_sort[$va['sort_id']] = $va['game_sort_name'];
        }
        $game_sort_names = array();
        foreach ($game_list as $key => $val){
            $game_sort_names[$val['id']]['app_name'] = $val['app_name'];
            $game_sort_names[$val['id']]['pack_name'] = $game_sort[$val['sort_id']];
        }
        $sdate     =  $request->input('date') ? strtotime(substr($request->input('date'),0,10).'00:00:00'): strtotime(date("Y-m-d 00:00:00", time()));
        $edate     =  $request->input('date') ? strtotime(substr($request->input('date'),11,10).' 23:59:59 ') : strtotime(date("Y-m-d 23:59:59", time()));

        $user_name = $request->input('user_name');
        $orderid = $request->input('orderid');
        $game_id = $request->input('game_id');
        $trade_orderid = $request->input('trade_orderid');
        $server_id = $request->input('server_id');
        $pay_channel = $request->input('pay_channel');
        $pay_money = $request->input('pay_money');
        $pay_succ = $request->input('pay_succ');

        //datatables 服务器模式
        $order_column  = $request->input('order');
        $end = $request->input('length')?$request->input('length'):20;

        $where = [
            ['pay_date','>=',"$sdate"],
            ['pay_date','<=',"$edate"],
        ];
        $db_pay = DB::connection('mysql_pay');
        $res = $db_pay->table('db_pay.pay_orders')
            ->where($where)
            ->when($user_name,function ($query) use ($user_name){
                return $query->where('user_name','=',$user_name);
            })
            ->when($orderid,function ($query) use ($orderid){
                return $query->where('orderid','=',$orderid);
            })
            ->when($trade_orderid,function ($query) use ($trade_orderid){
                return $query->where('trade_orderid','=',$trade_orderid);
            })
            ->when($server_id,function ($query) use ($server_id){
                return $query->where('server_id','=',$server_id);
            })
            ->when($pay_channel,function ($query) use ($pay_channel){
                return $query->where('pay_channel','=',$pay_channel);
            })
            ->when($game_id,function ($query) use ($game_id){
                return $query->whereIn('game_id',$game_id);
            })
            ->when($order_column,function ($query) use ($order_column){
                $i = intval($order_column[0]['column']);
                $order_arr = ['user_name','role_name','user_ip','orderid','trade_orderid','pay_date','money','pay_gold',11=>'succ',12=>'stat'];
                return $query->orderBy($order_arr[$i],$order_column[0]['dir']);
            })
            ->paginate($end);
        $payChannel = Cache::get('payChannel');
        $total_sum = 0;
        foreach ($res as $k=>$v) {
            //统计累计充值
            if($v->succ) $total_sum  += intval($v->money);
            $game_id                    = $v->game_id;
            $orderid                    = $v->orderid;
            $game_row                   = $db_pay->table('db_pay.game_list')->select('game_byname')->where(['id'=>$game_id])->get();
            $game_table                 = "pay_" . trim($game_row[0]->game_byname) . "_log";
            $pay_row                    = $db_pay->table($game_table)->select(['stat','pay_gold','back_result'])->where(['orderid'=>$orderid])->get();
//            $result[$orderid]['area'] = $v->user_ip?geoip()->getLocation(long2ip(intval($v->user_ip)))->getDisplayNameAttribute():'';
//            $address = Ip::find(long2ip(intval($v->user_ip)));
            $address = '';
//            $result[$orderid]['area'] = $v->user_ip?$address['country'].$address['region'].$address['city']:'';
            $result[$orderid]['area'] = '';
            $result[$orderid]['stat'] = $pay_row[0]->stat;
        }
        $assign=[
            'payChannel'=>$payChannel,
            'data'=>$res,
            'result'=>$result,
            'filters'=>[
                'server_id'=>$request->server_id,
                'pay_succ'=>$request->pay_succ,
                'orderid'=>$request->orderid,
                'trade_orderid'=>$request->trade_orderid,
                'pay_channel'=>$request->pay_channel,
                'pay_money'=>$request->pay_money,
                'user_name'=>$request->user_name,
                'date'=>$request->date,
                'game_id'=>$request->game_id?$request->game_id:array(),
            ],
            'game_sort_names'=>$game_sort_names,
            'game_list'=>$game_list,
            'game_sort_list'=>$game_sort_list,
        ];

        return view('operator.pay.payListQuery',$assign);
    }

    //支付成功 支付状态失败无法补发订单入库
    public function failOrderInsert(Request $request){
        $orderid = $request->orderid;
        //检测该订单,如果支付状态为成功则禁止操作
        $db_pay = DB::connection('mysql_pay');
        $result = $db_pay->table('db_pay.pay_orders')->select(['orderid','succ'])->where(['orderid'=>$orderid])->get();
        if(empty($result)){
            $returns = [
                'status' => 300,
                'message' => '该订单不存在!',
            ];
            return response()->json($returns);
        }
        /*if($result['succ'] == 1){
            ajaxReturn("请勿提交支付成功订单！",300);
        }*/
        //如果该订单已存在，禁止提交
        $res = $db_pay->table('db_pay.game_orders')->select(['id'])->where(['orderid'=>$orderid])->get()->toArray();
        if($res){
            $returns = [
                'status' => 300,
                'message' => '该订单已存在，请勿重复提交!',
            ];
            return response()->json($returns);
        }
        //入库操作
        $r = $db_pay->table('db_pay.game_orders')->insert(['orderid'=>$orderid,'add_time'=>time(),'status'=>0]);
        if($r){
            $returns = [
                'status' => 200,
                'message' => '提交成功!',
            ];
            return response()->json($returns);
        }else{
            $returns = [
                'status' => 300,
                'message' => '提交失败!',
            ];
            return response()->json($returns);
        }
    }

    //玩家LTV
    public function ltv(Request $request)
    {
        $game_list = $this->getPlatsGamesServers(2, 1, 0, 0, 0, 0, 0, 0, 2);
        $game_sort_list = $this->getGameSorts();
        $extend_list = $this->getExtendList();

        $sdate     =  $request->input('date') ? substr($request->input('date'),0,10) : date("Y-m-d", time() - 86400 * 6);
        $edate     =  $request->input('date') ? substr($request->input('date'),11,10) : date("Y-m-d", time());
        $agent_type = $request->input('agent_type');
        $extend_id = $request->input('extend_id');
        $agent_id = $request->input('agent_id');
        $site_id = $request->input('site_id');
        $site_id_excluded = $request->input('site_id_excluded');
        $game_id = $request->input('game_id');

        $day_arr = array('reg', 1, 2, 3, 4, 5, 6, 7, 10, 15, 30, 45, 60, 90, 120, 150, 180);

        if($agent_id && $extend_id ){
            $returns = [
                'status' => 300,
                'message' => '请勿同时选择渠道id和推广列表',
            ];
            return response()->json($returns);
        }

        $data = $data_tmp = $total = array();
        if ($game_id){
            $query = DB::connection('mysql_opgroup');
            if ($extend_id){
                $extend_agent = $query->table('sy_unions.agent')->select(['agent_id'])->where(['extend_id'=>$extend_id])->get();
                $extend_ids = [];
                foreach ($extend_agent as $v) {
                    $extend_ids[] = $v->agent_id;
                }
            }

            //查询注册
            $table = 'sy_center.sy_game_total_day';
            $columns = ' sum(reg_total) as reg,tdate ';
            $where = [
                ['tdate','>=',"$sdate"],
                ['tdate','<=',"$edate"],
            ];
            $result = $query->table($table)
                ->select(DB::raw($columns))
                ->where($where)->groupBy(['tdate'])
                ->when($agent_id,function ($query) use ($agent_id){
                    return $query->where('agent_id','=',$agent_id);
                })
                ->when($extend_ids,function ($query) use ($extend_ids){
                    return $query->whereIN('agent_id',$extend_ids);
                })
                ->when($site_id_excluded,function ($query) use ($site_id_excluded){
                    return $query->whereNotIN('site_id',$site_id_excluded);
                })
                ->when($game_id,function ($query) use ($game_id){
                    return $query->whereIn('game_id',$game_id);
                })
                ->when($site_id,function ($query) use ($site_id){
                    return $query->where('site_id','=',$site_id);
                })
                ->orderBy('tdate')
                ->get();
            $result = toArray($result);
            foreach ($result as $val) {
                $data_reg[$val['tdate']] = $val['reg'];
                $total['reg'] += $data_reg[$val['tdate']];
            }
            //查询支付
            $pay_tdate_table = 'sy_center.sy_pay_tdate';
            $columns = ' sum(pay_bet) as mo,reg_time as tdate,days ';
            $where = [
                ['reg_time','>=',$sdate],
                ['reg_time','<=',$edate],
                ['days','<=',180],
            ];
            $res = $query->table($pay_tdate_table)
                ->select(DB::raw($columns))
                ->where($where)->groupBy(['reg_time','days'])
                ->when($agent_id,function ($query) use ($agent_id){
                    return $query->where('agent_id','=',$agent_id);
                })
                ->when($extend_ids,function ($query) use ($extend_ids){
                    return $query->whereIN('agent_id',$extend_ids);
                })
                ->when($site_id_excluded,function ($query) use ($site_id_excluded){
                    return $query->whereNotIN('site_id',$site_id_excluded);
                })
                ->when($game_id,function ($query) use ($game_id){
                    return $query->whereIn('game_id',$game_id);
                })
                ->when($site_id,function ($query) use ($site_id){
                    return $query->where('site_id','=',$site_id);
                })
                ->orderBy('tdate')
                ->orderBy('days')
                ->get();
            $res = toArray($res);

            foreach ($res as $val) {
                $ltv = round($val['mo'] / $data_reg[$val['tdate']], 2);
                $data_tmp[$val['tdate']] += $ltv;
                $data[$val['tdate']]['reg'] = $data_reg[$val['tdate']];
                $data[$val['tdate']][$val['days']] = $data_tmp[$val['tdate']];
                $total[$val['days']]['dn']++;

            }
            //总LTV：每个日期的首天充值累加/总注册数
            $ress = $query->table($pay_tdate_table)
                ->select(DB::raw($columns))
                ->where($where)->groupBy(['days'])
                ->when($agent_id,function ($query) use ($agent_id){
                    return $query->where('agent_id','=',$agent_id);
                })
                ->when($extend_ids,function ($query) use ($extend_ids){
                    return $query->whereIN('agent_id',$extend_ids);
                })
                ->when($site_id_excluded,function ($query) use ($site_id_excluded){
                    return $query->whereNotIN('site_id',$site_id_excluded);
                })
                ->when($game_id,function ($query) use ($game_id){
                    return $query->whereIn('game_id',$game_id);
                })
                ->when($site_id,function ($query) use ($site_id){
                    return $query->where('site_id','=',$site_id);
                })
                ->orderBy('days')
                ->get();
            $ress = toArray($ress);
            $tmp_money = 0;
            foreach ($ress as $r) {
                $total[$r['days']]['ltv'] = ($r['mo']+$tmp_money)/$total['reg'];
                $tmp_money += $r['mo'];
            }
        }
//        dd($data);
        $assign=[
            'data'=>$data,
            'total'=>$total,
            'filters'=>[
                'agent_id'=>$request->agent_id,
                'site_id'=>$request->site_id,
                'site_id_excluded'=>$request->site_id_excluded,
                'date'=>$request->date,
                'game_id'=>$request->game_id?$request->game_id:array(),
            ],
            'extend_list'=>$extend_list,
            'day_arr'=>$day_arr,
            'game_list'=>$game_list,
            'game_sort_list'=>$game_sort_list,
        ];

        return view('operator.data.ltv',$assign);
    }

    public function total(Request $request)
    {
        $game_list = $this->getPlatsGamesServers(2, 1, 0, 0, 0, 0, 0, 0, 2);
        $game_sort_list = $this->getGameSorts();
        $extend_list = $this->getExtendList();

        $sdate     =  $request->input('date') ? substr($request->input('date'),0,10) : date("Y-m-d", time() - 86400 * 6);
        $edate     =  $request->input('date') ? substr($request->input('date'),11,10) : date("Y-m-d", time());
        $agent_type = $request->input('agent_type');
        $extend_id = $request->input('extend_id');
        $agent_id = $request->input('agent_id');
        $site_id = $request->input('site_id');
        $site_id_excluded = $request->input('site_id_excluded');
        $game_id = $request->input('game_id');

        $table = 'sy_center.sy_game_total_day';

        $query = DB::connection('mysql_opgroup')->table($table);
        $columns = ' tdate,sum(reg_total) as reg_total,sum(login_total) as login_total,sum(active_total) as active_total,sum(pay_total) as pay_total,sum(pay_total) as pay_amount,sum(pay_num) as pay_num,sum(old_tdate) as old_login_total,sum(old_login_next) as old_login_next,sum(old_pay_total) as old_pay_total,sum(old_pay_total) as old_pay_amount,sum(old_pay_num) as old_pay_num,sum(pay_tdate) as pay_tdate,sum(pay_num_tdate) as pay_num_tdate ';

        $res = $query
            ->select(DB::raw($columns))
            ->where($where)->groupBy(['tdate'])
            ->when($agent_id,function ($query) use ($agent_id){
                return $query->where('agent_id','=',$agent_id);
            })
            ->when($site_id,function ($query) use ($site_id){
                return $query->where('site_id','=',$site_id);
            })
            ->when($game_id,function ($query) use ($game_id){
                return $query->whereIn('game_id',$game_id);
            })
            ->orderBy('tdate')
            ->get();

        $db_pay = DB::connection('mysql_pay');
        $res = $db_pay->table('db_pay.pay_orders')
            ->where($where)
            ->when($user_name,function ($query) use ($user_name){
                return $query->where('user_name','=',$user_name);
            })
            ->when($orderid,function ($query) use ($orderid){
                return $query->where('orderid','=',$orderid);
            })
            ->when($trade_orderid,function ($query) use ($trade_orderid){
                return $query->where('trade_orderid','=',$trade_orderid);
            })
            ->when($server_id,function ($query) use ($server_id){
                return $query->where('server_id','=',$server_id);
            })
            ->when($pay_channel,function ($query) use ($pay_channel){
                return $query->where('pay_channel','=',$pay_channel);
            })
            ->when($game_id,function ($query) use ($game_id){
                return $query->whereIn('game_id',$game_id);
            })
            ->when($order_column,function ($query) use ($order_column){
                $i = intval($order_column[0]['column']);
                $order_arr = ['user_name','role_name','user_ip','orderid','trade_orderid','pay_date','money','pay_gold',11=>'succ',12=>'stat'];
                return $query->orderBy($order_arr[$i],$order_column[0]['dir']);
            })
            ->paginate($end);

        $assign=[
            'data'=>$data,
            'total'=>$total,
            'filters'=>[
                'agent_id'=>$request->agent_id,
                'site_id'=>$request->site_id,
                'site_id_excluded'=>$request->site_id_excluded,
                'date'=>$request->date,
                'game_id'=>$request->game_id?$request->game_id:array(),
            ],
            'extend_list'=>$extend_list,
            'day_arr'=>$day_arr,
            'game_list'=>$game_list,
            'game_sort_list'=>$game_sort_list,
        ];

        return view('operator.hm_channel.total',$assign);
    }

    public function test()
    {
        //
    }
}
