<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 15/12/15
 * Time: 3:27 PM
 */

namespace MenuManager\Admin;


class AdminMenuManager {

    public function initialize()
    {
        $this->setupSidebarmenu();
        $this->setupNavbarMenu();
        $this->setupNavbarUserActionsMenu();
    }

    public function setupSidebarmenu()
    {
        \Menu::make('AdminSidebarMenu', function($menu){
            $menuOrder = 1;
            $menu->add('Dashboard', ['route'    =>  'admin', 'icon' => 'fa fa-fw fa-dashboard'])->data('order', $menuOrder++);
            $categoriesMenu = $menu->add('Categories', ['icon' => 'fa fa-fw fa-folder-o'])->data('order', $menuOrder++);
                $categoriesMenu->add('Create', ['route'    =>  'adminCategoriesAddEdit', 'icon' => 'fa fa-fw fa-plus']);
                $categoriesMenu->add('View', ['route'    =>  'adminCategories', 'icon' => 'fa fa-fw fa-eye']);

            $postsMenu = $menu->add('Quizzes', ['icon' => 'fa fa-fw fa-file-text-o'])->data('order', $menuOrder++);
                $postsMenu->add('Create Quizzes', ['url'    =>  url('/admin/quizes/create'), 'icon' => 'fa fa-fw fa-plus']);
                $postsMenu->add('View Quizzes', ['url'    =>  url('/admin/quizes/view'), 'icon' => 'fa fa-fw fa-eye']);
                $postsMenu->add('Quiz Settings', ['route'    =>  'adminConfigQuiz', 'icon' => 'fa fa-fw fa-gear']);
                $postsMenu->add('Quiz Embed codes', ['route'    =>  'adminQuizesEmbedCodes', 'icon' => 'fa fa-fw fa-code']);

            $usersMenu = $menu->add('Users', ['icon' => 'fa fa-fw fa-user'])->data('order', $menuOrder++);
                $usersMenu->add('All users', ['route'    =>  'adminUsersHome', 'icon' => 'fa fa-fw fa-users']);
                $usersMenu->add('Quiz users', ['route'    =>  'adminQuizUsers', 'icon' => 'fa fa-fw fa-users']);

            $settingsMenu = $menu->add('Settings', ['icon' => 'fa fa-fw fa-gears'])->data('order', $menuOrder++);
                $settingsMenu->add('Main Settings', ['route'    =>  'adminConfig', 'icon' => 'fa fa-fw fa-gear']);
                $settingsMenu->add('Quiz Settings', ['route'    =>  'adminConfigQuiz', 'icon' => 'fa fa-fw fa-file-text-o']);
                $settingsMenu->add('Social Sharing Settings', ['route'    =>  'adminConfigSocialSharing', 'icon' => 'fa fa-fw fa-facebook-square']);
                $settingsMenu->add('Leaderboard Settings', ['route'    =>  'adminConfigLeaderboard', 'icon' => 'fa fa-fw fa-users']);


            $menu->add('Widgets', ['route'    =>  'adminConfigWidgets', 'icon' => 'fa fa-fw fa-puzzle-piece'])->data('order', $menuOrder++);
            $menu->add('Languages', ['route'    =>  'adminConfigLanuages', 'icon' => 'fa fa-fw fa-language'])->data('order', $menuOrder++);

            $pagesMenu = $menu->add('Pages', ['icon' => 'fa fa-fw fa-files-o'])->data('order', $menuOrder++);
                $pagesMenu->add('Create', ['route'    =>  'adminCreatePage', 'icon' => 'fa fa-fw fa-plus']);
                $pagesMenu->add('View', ['route'    =>  'adminViewPages', 'icon' => 'fa fa-fw fa-eye']);

            //$menu->add('Re-generate Sitemap', ['url'    =>  action('AdminSitemapController@getRegenerate'), 'icon' => 'fa fa-fw fa-sitemap'])->data('order', $menuOrder++);
            $menu->add('ShortCodes', ['route'    =>  'adminShortCodes', 'icon' => 'fa fa-fw fa-star'])->data('order', $menuOrder++);
            $menu->add('Update', ['route'    =>  'update', 'icon' => 'fa fa-fw fa-rocket'])->data('order', $menuOrder++);
            $menu->add('Change password', ['route'    =>  'adminChangePassword', 'icon' => 'fa fa-fw fa-key'])->data('order', $menuOrder++);
        });
    }

    public function setupNavbarMenu()
    {
        \Menu::make('AdminNavbarMenu', function($menu) {
            $menuOrder = 1;
            $menu->add('View Site', ['route' => 'home'])->prepend('<i class="fa fa-fw fa-laptop"></i> ')->data('order', $menuOrder++)->link->attr(['class' =>  'navbar-view-site-link', 'target'   =>  '_blank']);
        });
    }

    public function setupNavbarUserActionsMenu()
    {
        \Menu::make('AdminNavbarUserActionsMenu', function($menu) {
            $menuOrder = 1;
            $menu->add('Change Password', ['route' => 'adminChangePassword'])->prepend('<i class="fa fa-fw fa-key"></i> ')->data('order', $menuOrder++);
            $menu->add('Log out', ['route' => 'adminLogout'])->prepend('<i class="fa fa-fw fa-power-off"></i> ')->data('order', $menuOrder++);
        });
    }

    public function markNewMenuItems(){

        self::markItemAsNew('plugins');
        self::markItemAsNew('themes');
    }

    public static function markItemAsNew($itemKey)
    {
        $adminSidebarMenu = \Menu::get('AdminSidebarMenu');
        $menuItem = $adminSidebarMenu->item($itemKey);
        if($menuItem)
            $menuItem->data('new', true);
    }
}