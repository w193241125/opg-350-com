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

function getPlatsGamesServers($type = 1, $plat_id = 0, $game_id = 0, $server_id = 0, $is_open = 0, $open_game = 0, $is_vip = 0, $is_vip_limit = 0){
    \Illuminate\Support\Facades\DB::connection('db_opgroup')->table('db_center.wd_plat_list')->orderBy('id')->get();
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
