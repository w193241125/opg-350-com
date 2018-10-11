<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    /**
     * 获取 平台 游戏 服务器相关信息
     * +----------------------------------------------------------
     * @param int $type 获取类型 值分别可为 10更新缓存 1平台 2游戏 3服务器 4开发厂商
     * @param int $plat_id 若传，则返回具体平台的关于($type)数组
     * @param int $game_id 若传，$plat_id也必须传，返回具体平台具体游戏的关于($type)数组
     * @param int $server_id 若传，$plat_id,$game_id也必须传，返回具体平台具体游戏具体服的关于($type)数组
     * @param int $is_open 若为1时则只返回当前属于开启充值的游戏或服务器数组
     * @param int $open_game 若为1时则只返回当前属于尚在运行的游戏
     * @param int $is_vip 若为1时则只返回当前开通了超级用户的游戏
     * @param int $is_vip 若为1时则只返回大客户部门自己花费的权限游戏
     * +----------------------------------------------------------
     * @return array
    +----------------------------------------------------------
     */
    public function getPlatsGamesServers($type = 1, $plat_id = 0, $game_id = 0, $server_id = 0, $is_open = 0, $open_game = 0, $is_vip = 0, $is_vip_limit = 0)
    {
        $PlatsGamesServers = Cache::get('PlatsGamesServers');
        if (!$PlatsGamesServers || $type == 10) {
            $re1 = DB::connection('mysql_opgroup')->table('db_center.wd_plat_list')->orderBy('id')->get()->toArray();
            foreach ($re1 as $k1 => $v1) {
                $PlatsGamesServers['plats'][ $v1->id ] = json_decode(json_encode($v1), true);
            }
            $re2 = DB::connection('mysql_opgroup')->table('db_center.wd_game_list')->orderBy('id')->get()->toArray();
            foreach ($re2 as $k2 => $v2) {
                $tmpPlatId = $v2->plat_id;
                $PlatsGamesServers['games'][ $tmpPlatId ][ $v2->id ] = json_decode(json_encode($v2), true);
            }
            $re3 = DB::connection('mysql_opgroup')->table('db_center.wd_game_server_list')->orderBy('id')->get()->toArray();
            foreach ($re3 as $k3 => $v3) {
                if ($PlatsGamesServers['games'][ $v3->plat_id ][ $v3->game_id ]['open_game'] == 1) {//停服了的游戏 不读取服务器
                    $PlatsGamesServers['servers'][ $v3->plat_id ][ $v3->game_id ][ $v3->server_id ] = json_decode(json_encode($v3), true);
                }
            }
            $re4 = DB::connection('mysql_opgroup')->table('db_center.wd_game_company')->orderBy('id')->get()->toArray();
            foreach ($re4 as $k4 => $v4) {
                $PlatsGamesServers['company'][ $v4->id ] = json_decode(json_encode($v4), true);
            }
            Cache::put('PlatsGamesServers', $PlatsGamesServers, 1800);

            //充值渠道
            $re5 = DB::connection('mysql_opgroup')->table('db_center.wd_pay_channel')->where(['state'=>1])->orderBy('id')->get()->toArray();
            foreach ($re5 as $k5 => $v5) {
                $channel[ $v5['id'] ] = json_decode(json_encode($v5), true);
            }
            Cache::put('payChannel', $channel, 0);
        }

        if ($type < 10){
            if ($type == 1 && $plat_id > 0) {
                return $PlatsGamesServers['plats'][ $plat_id ];
            }elseif ($type == 1) {
                return $PlatsGamesServers['plats'];
            }elseif ($type == 2 && $plat_id > 0) {
                $games = $PlatsGamesServers['games'][$plat_id];
                return $games;
            }elseif ($type == 2) {
                return $PlatsGamesServers['games'];
            } elseif ($type == 3 && $plat_id > 0 && $game_id > 0 && $server_id > 0) {
                return $PlatsGamesServers['servers'][ $plat_id ][ $game_id ][ $server_id ];
            } elseif ($type == 3 && $plat_id > 0 && $game_id > 0) {
                $servers = $PlatsGamesServers['servers'][ $plat_id ][ $game_id ];
                if ($servers) {
                    foreach ($servers as $key => $val) {
                        if ($is_open == 1 && $val['is_open'] != 1) unset($servers[ $key ]);
                    }
                }

                return $servers;
            } elseif ($type == 3 && $plat_id > 0) {
                $servers = $PlatsGamesServers['servers'][ $plat_id ];
                foreach ($servers as $key => $val) {
                    if ($is_open == 1 && $val['is_open'] != 1) unset($servers[ $key ]);
                }

                return $servers;
            } elseif ($type == 3) {
                return $PlatsGamesServers['servers'];
            } elseif ($type == 4) {
                return $PlatsGamesServers['company'];
            } else {
                return $PlatsGamesServers;
            }
        }
    }

    public function getGames2($plat_id) {
        $gamesArr = $this->getPlatsGamesServers(2, $plat_id);

        return $gamesArr;
    }
}
