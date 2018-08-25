<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Menu extends Model
{

    /**
     * 递归查询子分类
     * @param int $parent_id 父类ID
     * @return array
     */
    public function getAllMenu($parent_id = 0) {

        $menus = $this->where('parent_id' , $parent_id)->orderBy('order' ,'asc')->get();
        $all_menus = array();
        if (!empty($menus)) {
            foreach ($menus as $key => $menu) {
                $all_menus[$menu->id] = $menu;
                //查询子菜单
                $menu_child = $this->getAllMenu($menu->id);
                if (!empty($menu_child)) {
                    //子菜单不为空放在 child 数组中
                    $all_menus[$menu->id]['child'] = $menu_child;
                }
            }
        }
        return $all_menus;
    }

    /**
     * 从缓存或者数据库中读取左侧菜单数据
     * @return array
     * */
    public function getMenuList()
    {
        if (Cache::has('admin.overall_menus')) {
            $menus = Cache::get('admin.overall_menus');
        } else {
            $menus = $this->model_menu->getAllMenu();
        }
        return $menus;
    }
}
