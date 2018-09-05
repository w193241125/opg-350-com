<?php

namespace App\Http\Controllers\System;

use App\Http\Requests\MenuTablePost;
use App\Models\Menu;
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
     * Show the form for creating a new resource.
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
     * Show the form for editing the specified resource.
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
     * Update the specified resource in storage.
     *
     * @param  MenuTablePost  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MenuTablePost $request, $id)
    {
        $responseData = $this->menu->updateMenu($id, $request);
        return response()->json($responseData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->menu->delAllMenu($id);
        $this->menu->setMenuAllCache();
        return response()->json(['state' => 'success']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createMenu()
    {
        //读取区父级分类
        $menu_first = Menu::where('parent_id', 0)->orderBy('order', 'asc')->get();

        return view('system.menu_add', ['menu_first' => $menu_first]);
    }

    public function add_menus()
    {

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
