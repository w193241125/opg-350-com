<?php

namespace App\Http\Controllers\System;

use App\Http\Requests\MenuTablePost;
use App\Models\Menu;
use App\Models\MyPermission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    protected $menu;

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    /**
     * 获取所有菜单
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $all_menu = $this->menu->getAllMenu();

        $assign = [
            'all_menu'=>$all_menu,
        ];
        return view('system.menu',$assign);
    }

    /**
     * 展示菜单创建页面
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //读取区父级分类
        $menu_first = Menu::where('parent_id', 0)->orderBy('order', 'asc')->get();
        return view('admin.menu.menu_add', ['menu_first' => $menu_first]);
    }

    /**
     * 保存新建菜单
     *
     * @param  MenuTablePost  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MenuTablePost $request)
    {

        if ($request->parent_id_1 == 0) {
            $parent_id = 0;
        } else {
            if ($request->parent_id_2 == 0) {
                $parent_id = $request->parent_id_1;
            } else {
                $parent_id = $request->parent_id_2;
            }
        }
        $responseData = $this->menu->createMenu($parent_id, $request);
        return response()->json($responseData);
    }

    /**
     * 展示编辑菜单页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu_info = Menu::find($id);
        return view('system.menu_edit' ,['menu_info' => $menu_info]);
    }

    /**
     * 执行菜单更新
     *
     * @param  MenuTablePost  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MenuTablePost $request, $id)
    {
        //获取此 id 菜单名
        $menu_uri = $this->menu->getMenuUri($id);
        //更新权限名称
        MyPermission::updPmByName($menu_uri, $request->uri);
        //清除缓存
        MyPermission::clearCache();
        $responseData = $this->menu->updateMenu($id, $request);
        return response()->json($responseData);
    }

    /**
     * 删除菜单
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //获取此 id 下所有菜单 名
        $menuName = $this->menu->getAllMenuUri($id);

        //删除权限表对应菜单
        MyPermission::delPmByName($menuName);

        //删除菜单
        $this->menu->delAllMenu($id);
        $this->menu->setMenuAllCache();
        return response()->json(['state' => 'success']);
    }

    /**
     * 创建菜单
     *
     * @return \Illuminate\Http\Response
     */
    public function createMenu()
    {
        //读取父级分类
        $menu_first = Menu::where('parent_id', 0)->orderBy('order', 'asc')->get();

        return view('system.menu_add', ['menu_first' => $menu_first]);
    }

    /**
     * 保存排序
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function saveOrder(Request $request)
    {
        if (!empty($request->menu)) {
            $menu = json_decode($request->menu);
            $this->menu->saveMenuOrder($menu);
            $this->menu->setMenuAllCache();
            return response()->json(['state' => 'success']);
        }
    }

    /**
     * AJAX获取二级分类
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function ajaxGetChildMenu(Request $request) {
        if ($request->parent_id != 0) {
            return $this->menu->getChildMenu($request->parent_id);
        }
    }


}
