<?php
/**
 * 用户权限html组件
 * Created by PhpStorm.
 * User: Larwas
 * Date: 2018/11/1
 * Time: 11:00
 */

namespace App\Presenters;

class userPermissionsPresenter
{
    /**
     * 将用户权限分组排列
     * @param array $permissions 权限
     * @param array $userPer 用户组
     * @return string
     * */
    public function getPermissions($permissions, $userPer = [])
    {
        dd(111);
        $html = '';
        $permissions = $this->groupPermissionsByType($permissions);
        $userPer = $this->getUserPermissions($userPer);
        foreach ($permissions as $key => $permission) {
            $key = htmlspecialchars($key);
            $html .= "<tr><td><span class='label label-sm label-success'>".$key." </span></td><td>";
            if (is_array($permission)){
                foreach ($permission as $value) {
                    $display_name = htmlspecialchars($value['pm_display_name']);
                    $checked = in_array($value['id'], $userPer) ? 'checked' : '';
                    $html .= <<<Eof
                <div class="md-checkbox col-md-4">
                  <input type="checkbox" id="permission_{$value['id']}" class="md-check" value="{$value['id']}" name="permission[]" {$checked}>
                  <label for="permission_{$value['id']}">
                        <span></span>
                        <span class="check"></span>
                        <span class="box"></span> {$display_name}
                  </label>
                </div>
Eof;
                }
            }else{
                $display_name = htmlspecialchars($permission->pm_display_name);
                $checked = in_array($permission->id, $userPer) ? 'checked' : '';
                $html .= <<<Eof
                <div class="md-checkbox col-md-4">
                  <input type="checkbox" id="permission_{$permission->id}" class="md-check" value="{$permission->id}" name="permission[]" {$checked}>
                  <label for="permission_{$permission->id}">
                        <span></span>
                        <span class="check"></span>
                        <span class="box"></span> {$display_name}
                  </label>
                </div>
Eof;
            }
            

            $html .= '</td></tr>';
        }
        return $html;
    }

    /**
     * 权限组重新排序，按权限分组
     * @param array $permissions
     * @return array
     * */
    public function groupPermissions($permissions)
    {
        dd(111);
        $array = [];
        $array = array_sorts(toArray($permissions),'pm_type');
        return $array;
    }

    /**
     * 获取当前用户组权限ID数组
     * @param object $userPer
     * @return array
     * */
    public function getUserPermissions($userPer)
    {
        dd(111);
        $array = [];
        if (!empty($userPer)) {
            foreach ($userPer as $permission) {
                $array[] = $permission->id;
            }
        }
        return $array;
    }

    /**
     * 权限组重新排序，按权限分组
     * @param array $permissions
     * @return array
     * */
    public function groupPermissionsByType($permissions)
    {
        dd(111);
        $arr_sort = [];
        $pm = json_decode(json_encode($permissions),true);
        foreach ($pm as $k=>$permission) {
            $arr_sort[$permission['pm_type']][]=$permission;
        }
        return $arr_sort;
    }
}