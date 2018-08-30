<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Menu extends Model
{

    /**
     * 保存菜单
     * @param int $parent_id
     * @param object $request
     * @return array
     * */
    public function createMenu($parent_id, $request)
    {
        $data = [];
        $data['parent_id'] = $parent_id;
        $data['name'] = $request->name;
        $data['icon'] = $request->icon;
        $data['uri'] = $request->uri;
        if ($this->save($data)) {
            $result = $this->setMenuAllCache();
        } else {
            $result = false;
        }
        return [
            'status' => $result,
            'message' => $result ? '菜单添加成功':'菜单添加失败',
        ];
    }

    /**
     * 获取子菜单
     * @param int $parent_id
     * */
    public function getChildMenu($parent_id)
    {
        return Menu::where('parent_id', $parent_id)->orderBy('order', 'asc')->get();
    }

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

    /**
     * 保存菜单排序
     * @param object $menus
     * @param int $parent_id
     * */
    public function saveMenuOrder($menus ,$parent_id = 0)
    {
        foreach ($menus as $key => $menu) {
            $model_menu = Menu::find($menu->id);
            $model_menu->parent_id = $parent_id;
            $model_menu->order = $key+1;
            if ($model_menu->save() && !empty($menu->children)) {
                $this->saveMenuOrder($menu->children, $menu->id);
            }
        }
    }

    /**
     * 更新菜单
     * @param int $id
     * @param object $request
     * @return array
     * */
    public function updateMenu($id, $request)
    {
        $menu_info = Menu::find($id);
        $menu_info->name = $request->name;
        $menu_info->icon = $request->icon;
        $menu_info->uri = $request->uri;
        if ($menu_info->save()) {
            $result = $this->setMenuAllCache();
        } else {
            $result = false;
        }
        return [
            'status' => $result,
            'message' => $result ? '菜单更新成功':'菜单更新失败',
        ];
    }

    /**
     * 递归删除菜单
     * @param int $id
     * */
    public function delAllMenu ($id)
    {
        $menu = Menu::find($id);
        if ($menu) {
            Menu::destroy($id);
            $child = Menu::where(['parent_id' => $menu->id])->get();
            if ($child) {
                foreach ($child as $value) {
                    $this->delAllMenu($value->id);
                }
            }
        }
    }

    /**
     * 生成菜单缓存
     * @return bool
     * */
    public function setMenuAllCache ()
    {
        $menus = $this->getAllMenu();
        Cache::forever('admin.overall_menus', $menus);
        return true;
    }
}
