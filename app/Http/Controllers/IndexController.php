<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * 首页
     *
     * @param Menu $menuModel
     * @return mixed
     */
    public function index(Menu $menuModel)
    {
        $menus = Menu::all();
        $assign = [
            'menus'=>$menus,
        ];

        return view('index',$assign);
    }
}
