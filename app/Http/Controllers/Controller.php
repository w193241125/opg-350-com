<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
            $db_opgroup = DB::connection('mysql_opgroup');
            $re1 = $db_opgroup->table('db_center.wd_plat_list')->orderBy('id','asc')->get()->toArray();
            $re1 = toArray($re1);
            foreach ($re1 as $k1 => $v1) {
                $PlatsGamesServers['plats'][ $v1['id'] ] = $v1;
            }
            $re2 = $db_opgroup->table('db_center.wd_game_list')->orderBy('id','asc')->get()->toArray();
            $re2 = toArray($re2);
            foreach ($re2 as $k2 => $v2) {
                $tmpPlatId = $v2['plat_id'];
                $PlatsGamesServers['games'][ $tmpPlatId ][ $v2['id'] ] = $v2;
            }
            $re3 = $db_opgroup->table('db_center.wd_game_server_list')->orderBy('id','asc')->get()->toArray();
            $re3 = toArray($re3);
            foreach ($re3 as $k3 => $v3) {
                if ($PlatsGamesServers['games'][ $v3['plat_id'] ][ $v3['game_id'] ]['open_game'] == 1) {//停服了的游戏 不读取服务器
                    $PlatsGamesServers['servers'][ $v3['plat_id'] ][ $v3['game_id'] ][ $v3['server_id'] ] = $v3;
                }
            }

            $re4 = $db_opgroup->table('db_center.wd_game_company')->orderBy('id','asc')->get()->toArray();
            $re4 = toArray($re4);
            foreach ($re4 as $k4 => $v4) {
                $PlatsGamesServers['company'][ $v4['id'] ] = $v4;
            }
            Cache::put('PlatsGamesServers', $PlatsGamesServers, 1800);

            //充值渠道
            $re5 = $db_opgroup->table('db_center.wd_pay_channel')->where(['state'=>1])->orderBy('order_id','asc')->get()->toArray();
            $re5 = toArray($re5);
            foreach ($re5 as $k5 => $v5) {
                $channel[ $v5['id'] ] = $v5;
            }
            Cache::put('payChannel', $channel);
        }
        if ($type < 10) {
            if ($type == 1 && $plat_id > 0) {
                return $PlatsGamesServers['plats'][ $plat_id ];
            } elseif ($type == 1) {
                return $PlatsGamesServers['plats'];
            } elseif ($type == 2 && $plat_id > 0 && $game_id > 0) {
                return $PlatsGamesServers['games'][ $plat_id ][ $game_id ];
            } elseif ($type == 2 && $plat_id > 0) {
                $games = $PlatsGamesServers['games'][ $plat_id ];
                if ($is_vip == 1) {
                    $re         = Cache::get('vip_admin');
                    $vip_gameid = $re['games'][ $plat_id ];
                }
                foreach ($games as $key => $val) {
                    //这里还将 区分某人是否有 某个游戏的权限
                    if ($_SESSION['gamelist'] != 'all' && is_array($_SESSION['gamelist'][ $plat_id ]) && $_SESSION['gamelist'][ $plat_id ] != 'all' && !in_array($key, $_SESSION['gamelist'][ $plat_id ])) {
                        unset($games[ $key ]);
                        continue;
                    }
                    if ($is_open == 1 && $val['is_open'] != 1) {
                        unset($games[ $key ]);
                        continue;
                    }
                    if ($open_game == 1 && $val['open_game'] != 1) {
                        unset($games[ $key ]);
                        continue;
                    }
                    if ($is_vip == 1 && !in_array($val['id'], $vip_gameid)) {
                        unset($games[ $key ]);
                        continue;
                    }
                    //大客户部门人员 游戏权限
                    if ($_SESSION['dept'] == 7 && $is_vip_limit == 1) {
                        $result   = Cache::get('vip_admin');
                        $userData = $result['user'][ $_SESSION['uName'] ];
                        if (is_array($userData['games'][ $plat_id ]) && !in_array($val['id'], $userData['games'][ $plat_id ]) && $userData['is_admin'] != 1) {
                            unset($games[ $key ]);
                            continue;
                        }
                    }
                }

                return $games;
            } elseif ($type == 2) {
                foreach ($PlatsGamesServers['plats'] as $pk => $pv) {
                    $games = $PlatsGamesServers['games'][ $pk ];
                    if ($is_vip == 1) {
                        $re         = Cache::get('vip_admin');
                        $vip_gameid = $re['games'][ $pk ];
                    }
                    foreach ($games as $key => $val) {
                        //这里还将 区分某人是否有 某个游戏的权限
                        if ($_SESSION['gamelist'] != 'all' && $_SESSION['gamelist'][ $pk ] != 'all' && !in_array($key, $_SESSION['gamelist'][ $pk ])) {
                            unset($PlatsGamesServers['games'][ $pk ][ $key ]);
                            continue;
                        }
                        if ($is_open == 1 && $val['is_open'] != 1) {
                            unset($PlatsGamesServers['games'][ $pk ][ $key ]);
                            continue;
                        }
                        if ($open_game == 1 && $val['open_game'] != 1) {
                            unset($PlatsGamesServers['games'][ $pk ][ $key ]);
                            continue;
                        }
                        if ($is_vip == 1 && !in_array($val['id'], $vip_gameid)) {
                            unset($PlatsGamesServers['games'][ $pk ][ $key ]);
                            continue;
                        }
                        //大客户部门人员 游戏权限
                        if ($_SESSION['dept'] == 7 && $is_vip_limit == 1) {
                            $result   = Cache::get('vip_admin');
                            $userData = $result['user'][ $_SESSION['uName'] ];
                            if (is_array($userData['games'][ $pk ]) && !in_array($val['id'], $userData['games'][ $pk ]) && $userData['is_admin'] != 1) {
                                unset($PlatsGamesServers['games'][ $pk ][ $key ]);
                                continue;
                            }
                        }
                    }
                }

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

    /**
     * 获取游戏分类
     * @return mixed
     */
    public function getGameSorts($type=1)
    {
        $game_sort_list = Cache::get('game_sort_list');
        if ($type==10){
            $game_sort_list = DB::connection('mysql_opgroup')->table('db_center.`wd_game_sort`')->get();
            Cache::put('game_sort_list',toArray($game_sort_list),1800);
        }
        return $game_sort_list;
    }

    /**
     * 获取上线的游戏
     * +----------------------------------------------------------
     * @param int $type 获取类型 值分别可为 10更新缓存 1游戏所有信息 2游戏按分类返回
     * +----------------------------------------------------------
     * @return array
    +----------------------------------------------------------
     */
    public function getPlatsGamesOnline($type=1)
    {
        if ($type==1){
            $PlatsGamesOnline = $this->getCache('PlatsGamesOnline');
        }elseif($type==2){
            $PlatsGamesOnline = $this->getCache('PlatsGamesOnlineClassified');
        }

        if (!$PlatsGamesOnline || $type==10){
            $PlatsGamesOnline = array();
            $re = $this->db->find("select * from db_center.wd_game_list WHERE `owner`=1 order by id asc");
            foreach ($re as $item) {
                $PlatsGamesOnline[$item['id']] = $item;
            }
            $this->makeCache('PlatsGamesOnline', $PlatsGamesOnline, 1800);
        }
        if ($type==2){
            $re = $this->db->find("select g.*, s.game_sort_name from db_center.wd_game_list as g LEFT JOIN db_center.wd_game_sort as s ON g.sort_id = s.sort_id WHERE `owner`=1 order by id asc");
            foreach ($re as $item) {
                $PlatsGamesOnlineClassified[$item['sort_id']]['game_sort_name'] = $item['game_sort_name'];
                $PlatsGamesOnlineClassified[$item['sort_id']]['game_id'][] = $item['id'];
                $PlatsGamesOnlineClassified[$item['sort_id']]['game_id_str'] .= $item['id'].',';
                $PlatsGamesOnlineClassified[$item['sort_id']][$item['id']] = $item['name'];
            }
            //将传奇来了计算区服的游戏 id 放到键位 0 上
            $PlatsGamesOnlineClassified[1]['game_id'][0]=3;
            $PlatsGamesOnlineClassified[1]['game_id'][2]=1;
            //官人我还要
            $PlatsGamesOnlineClassified[4]['game_id'][0]=35;
            $PlatsGamesOnlineClassified[4]['game_id'][1]=10;
            //梦道
            $PlatsGamesOnlineClassified[10]['game_id'][0]=57;
            $PlatsGamesOnlineClassified[10]['game_id'][1]=54;
            //萌宠
            $PlatsGamesOnlineClassified[16]['game_id'][0]=182;
            $PlatsGamesOnlineClassified[16]['game_id'][1]=181;

            foreach ($PlatsGamesOnlineClassified as $k=>$item) {
                $PlatsGamesOnlineClassified[$k]['game_id_str'] = trim($item['game_id_str'],',');
            }
            $this->makeCache('PlatsGamesOnlineClassified', $PlatsGamesOnlineClassified, 1800);
        }
        return $PlatsGamesOnline;
    }
}
