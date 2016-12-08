<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Tuan Linh
 * Date: 2015-09-24
 * Time: 15:39
 */

namespace Katniss\Everdeen\Themes\AdminThemes\AdminLte\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Katniss\Everdeen\Utils\DataStructure\Menu\Menu;
use Katniss\Everdeen\Utils\DataStructure\Menu\MenuRender;

class AdminMenuComposer
{

    /**
     * Create a new profile composer.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('admin_menu', $this->getMenuRender($this->getMenu()));
    }

    protected function getMenuRender(Menu $menu)
    {
        $menuRender = new MenuRender();
        $menuRender->wrapClass = 'sidebar-menu';
        $menuRender->childrenWrapClass = 'treeview-menu';
        return new HtmlString($menuRender->render($menu));
    }

    protected function getMenu()
    {
        $currentUrl = currentUrl();
        $user = authUser();
        $menu = new Menu($currentUrl);
        if ($user->can('access-admin')) {
            // Dashboard
            $menu->add( // add a menu item
                adminUrl(),
                trans('pages.admin_dashboard_title'), '<i class="fa fa-dashboard"></i> <span>', '</span>'
            );
            // My Account
            $menu->add( // add a menu item
                meUrl('account'),
                trans('pages.my_account_title'), '<i class="fa fa-user"></i> <span>', '</span>'
            );
            // My Settings
            $menu->add( // add a menu item
                meUrl('settings'),
                trans('pages.my_settings_title'), '<i class="fa fa-cog"></i> <span>', '</span>'
            );
            // File Manager
            $menu->add( // add a menu item
                adminUrl('my-documents'),
                trans('pages.my_documents_title'), '<i class="fa fa-file"></i> <span>', '</span>'
            );


            if ($user->hasRole('admin')) {
                // System Settings
                $menu->add( // add a menu header
                    null,
                    mb_strtoupper(trans('pages.admin_system_settings_title')),
                    '', '', 'header'
                );
                $menu->add( // add a menu item
                    adminUrl('user-roles'),
                    trans('pages.admin_roles_title'), '<i class="fa fa-unlock"></i> <span>', '</span>'
                );
                $menu->add( // add a menu item
                    adminUrl('users'),
                    trans('pages.admin_users_title'),
                    '<i class="fa fa-user"></i> <span>', '</span>'
                );
                // Theme Settings
                $menu->add( // add a menu header
                    null,
                    mb_strtoupper(trans('pages.admin_theme_settings_title')), '', '', 'header'
                );
                $menu->add( // add a menu item
                    adminUrl('app-options'),
                    trans('pages.admin_app_options_title'),
                    '<i class="fa fa-cogs"></i> <span>', '</span>'
                );
                $menu->add( // add a menu item
                    adminUrl('extensions'),
                    trans('pages.admin_extensions_title'),
                    '<i class="fa fa-cubes"></i> <span>', '</span>'
                );
                $menu->add( // add a menu item
                    adminUrl('widgets'),
                    trans('pages.admin_widgets_title'),
                    '<i class="fa fa-square-o"></i> <span>', '</span>'
                );
                $menu->add(  // add an example menu item which have sub menu
                    '#',
                    trans('pages.admin_ui_lang_title'),
                    '<i class="fa fa-newspaper-o"></i> <span>', '</span> <i class="fa fa-angle-left pull-right"></i>', 'treeview'
                );
                $subMenu = new Menu($currentUrl);
                $subMenu->add( // add a menu item
                    adminUrl('ui-lang/php'),
                    trans('pages.admin_ui_lang_php_title'), '<i class="fa fa-file-code-o"></i> <span>', '</span>'
                );
                $subMenu->add( // add a menu item
                    adminUrl('ui-lang/email'),
                    trans('pages.admin_ui_lang_email_title'), '<i class="fa fa-file-text-o"></i> <span>', '</span>'
                );
                $menu->addSubMenu($subMenu);

                //Links
                $menu->add( // add a menu header
                    null,
                    mb_strtoupper(trans('pages.admin_link_header')),
                    '', '', 'header'
                );
                $menu->add( //add a menu item
                    adminUrl('link-categories'),
                    trans('pages.admin_link_categories_title'),
                    '<i class="fa fa-table"></i> <span>', '</span>'
                );
                $menu->add( //add a menu item
                    adminUrl('links'),
                    trans('pages.admin_links_title'),
                    '<i class="fa fa-external-link"></i> <span>', '</span>'
                );

                //Links
                $menu->add( // add a menu header
                    null,
                    mb_strtoupper(trans('pages.admin_post_header')),
                    '', '', 'header'
                );
                $menu->add( //add a menu item
                    adminUrl('pages'),
                    trans('pages.admin_pages_title'),
                    '<i class="fa fa-file"></i> <span>', '</span>'
                );
                $menu->add( //add a menu item
                    adminUrl('article-categories'),
                    trans('pages.admin_article_categories_title'),
                    '<i class="fa fa-table"></i> <span>', '</span>'
                );
                $menu->add( //add a menu item
                    adminUrl('articles'),
                    trans('pages.admin_articles_title'),
                    '<i class="fa fa-align-justify"></i> <span>', '</span>'
                );
            }
        }
        $menu = contentFilter('admin_menu', $menu);
        return $menu;
    }
}