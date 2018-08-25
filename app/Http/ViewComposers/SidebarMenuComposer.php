<?php
/**
 * 菜单 View 数据共享层
 * Created by PhpStorm.
 * User: Larwas
 * Date: 2018年8月25日1
 * Time: 0:56:26
 */

namespace App\Http\ViewComposers;

use App\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class SidebarMenuComposer
{
    /**
     * 菜单
     *
     * @var Menu
     */
    protected $model_menu;

    /**
     * 创建一个新的属性composer.
     *
     * @param Menu $menu
     */
    public function __construct(Menu $menu)
    {
        // Dependencies automatically resolved by service container...
        $this->model_menu = $menu;
    }

    /**
     * 绑定数据到视图.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('overall_menus', $this->getMenuList());
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