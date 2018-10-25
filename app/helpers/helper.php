<?php
/**
 * 帮助函数
 * Created by PhpStorm.
 * User: Larwas
 * Date: 2018年9月27日
 * Time: 14:22:12
 * @author Larwas
 */


/**
 * 返回可读性更好的文件尺寸
 */
function human_filesize($bytes, $decimals = 2)
{
    $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .@$size[$factor];
}


// editor.md JS
if (!function_exists("editor_js_a")) {
    function editor_js_a()
    {
        return '
<script src="/vendor/editormd/js/editormd.js"></script>
<script src="/vendor/editormd/lib/marked.min.js"></script>
<script src="/vendor/editormd/lib/prettify.min.js"></script>
<script src="/vendor/editormd/lib/raphael.min.js"></script>
<script src="/vendor/editormd/lib/underscore.min.js"></script>
<script src="/vendor/editormd/lib/sequence-diagram.min.js"></script>
<script src="/vendor/editormd/lib/flowchart.min.js"></script>
<script src="/vendor/editormd/lib/jquery.flowchart.min.js"></script>
<script>
    var testEditor;
    $(function () {
        editormd.emoji = {
            path: "//staticfile.qnssl.com/emoji-cheat-sheet/1.0.0/",
            ext: ".png"
        };
        testEditor = editormd({
            id: "editormd_id",
            width: "' . config('editormd.width') . '",
            height:' . config('editormd.height') . ',
            theme: "' . config('editormd.theme') . '",
            editorTheme:"' . config('editormd.editorTheme') . '",
            previewTheme:"' . config('editormd.previewTheme') . '",
            path: \'/vendor/editormd/lib/\',
            codeFold:' . config('editormd.codeFold') . ',
            saveHTMLToTextarea: ' . config('editormd.saveHTMLToTextarea') . ',
            searchReplace: ' . config('editormd.searchReplace') . ',
            emoji: ' . config('editormd.emoji') . ',
            taskList: ' . config('editormd.taskList') . ',
            tocm: ' . config('editormd.tocm') . ',
            tex: ' . config('editormd.tex') . ',
            flowChart: ' . config('editormd.flowChart') . ',
            sequenceDiagram: ' . config('editormd.sequenceDiagram') . ',
            imageUpload: ' . config("editormd.imageUpload") . ',
            imageFormats:["jpg", "jpeg", "gif", "png", "bmp", "webp"],
            imageUploadURL: "'. config("editormd.imageUploadURL") .'?token=' . csrf_token() .'",
        });
    })
</script>
    ';
    }
}

if (!function_exists('flash_error')){
    /**
     * 添加失败提示
     *
     * @param string $message
     */
    function flash_error($message = '失败')
    {
        session()->flash('alert-message', $message);
        session()->flash('alert-type', 'error');
    }
}

if (!function_exists('flash_success')){
    /**
     * 添加成功提示
     *
     * @param string $message
     */
    function flash_success($message = '成功')
    {
        session()->flash('alert-message', $message);
        session()->flash('alert-type', 'success');
    }
}

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
function getPlatsGamesServers($type = 1, $plat_id = 0, $game_id = 0, $server_id = 0, $is_open = 0, $open_game = 0, $is_vip = 0, $is_vip_limit = 0){
    $PlatsGamesServers =\Illuminate\Support\Facades\Cache::get('PlatsGamesServers');
    if (!$PlatsGamesServers || $type == 10) {
        $db_opgroup = \Illuminate\Support\Facades\DB::connection('mysql_opgroup');
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
        \Illuminate\Support\Facades\Cache::put('PlatsGamesServers', $PlatsGamesServers, 1800);

        //充值渠道
        $re5 = $db_opgroup->table('db_center.wd_pay_channel')->where(['state'=>1])->orderBy('order_id','asc')->get()->toArray();
        $re5 = toArray($re5);
        foreach ($re5 as $k5 => $v5) {
            $channel[ $v5['id'] ] = $v5;
        }
        \Illuminate\Support\Facades\Cache::put('payChannel', $channel);
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
                $re         = \Illuminate\Support\Facades\Cache::get('vip_admin');
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
                    $result   = \Illuminate\Support\Facades\Cache::get('vip_admin');
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
                    $re         = \Illuminate\Support\Facades\Cache::get('vip_admin');
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
                        $result   = \Illuminate\Support\Facades\Cache::get('vip_admin');
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
 function getGameSorts($type=1)
{
    $game_sort_list = \Illuminate\Support\Facades\Cache::get('game_sort_list');
    if ($type==10 || empty($game_sort_list)){
        $game_sort_list = \Illuminate\Support\Facades\DB::connection('mysql_opgroup')->table('db_center.wd_game_sort')->get();
        \Illuminate\Support\Facades\Cache::put('game_sort_list',toArray($game_sort_list),1800);
    }
    return $game_sort_list;
}

function GetIP(){//获取IP
    if ($_SERVER["HTTP_X_FORWARDED_FOR"])
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    else if ($_SERVER["HTTP_CLIENT_IP"]) //PHP开源代码
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    else if ($_SERVER["REMOTE_ADDR"])
        $ip = $_SERVER["REMOTE_ADDR"];
    else if (getenv("HTTP_X_FORWARDED_FOR")) //PHP开源代码
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
        $ip = "Unknown";
    return $ip;
}

/**
 * 将集合转换为数组
 * @param $item
 * @return mixed
 */
function toArray($item){
    $arr = json_decode(json_encode($item),true);
    return $arr;
}


if (!function_exists('array_sorts')) {
    function array_sorts($arr, $str){
        $result = [];
        if (is_array($arr)){
            foreach ($arr as $item) {
                $result[$item[$str]][] = $item;
            }
        }
        return $result;
    }
}
