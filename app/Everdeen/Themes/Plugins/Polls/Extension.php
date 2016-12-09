<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Tuan Linh
 * Date: 2016-05-21
 * Time: 18:38
 */

namespace Katniss\Everdeen\Themes\Plugins\Polls;

use Katniss\Everdeen\Themes\Extension as BaseExtension;
use Katniss\Everdeen\Themes\Plugins\Polls\Controllers\ChoiceAdminController;
use Katniss\Everdeen\Themes\Plugins\Polls\Controllers\PollAdminController;
use Katniss\Everdeen\Utils\DataStructure\Menu\Menu;
use Katniss\Everdeen\Utils\ExtraActions\CallableObject;

class Extension extends BaseExtension
{
    const NAME = 'polls';
    const DISPLAY_NAME = 'Polls';
    const DESCRIPTION = 'Enable to embed polls to layout and manage polls and choices';
    const EDITABLE = false;

    public function __construct()
    {
        parent::__construct();
    }

    protected function __init()
    {
        parent::__init();

        _kWidgets([Widget::NAME => Widget::class]);
    }

    public function register()
    {
        addFilter('extra_admin_menu', new CallableObject(function (Menu $menu) {
            if (authUser()->hasRole('admin')) {
                $menu->add( // add a menu item
                    addExtraUrl('admin/polls', adminUrl('extra')),
                    trans('polls.page_polls_title'),
                    '<i class="fa fa-circle-o"></i> <span>', '</span>'
                );
                $menu->add( // add a menu item
                    addExtraUrl('admin/poll-choices', adminUrl('extra')),
                    trans('polls.page_poll_choices_title'),
                    '<i class="fa fa-circle-o"></i> <span>', '</span>'
                );
            }
            return $menu;
        }), 'ext:polls:menu');

        addExtraRouteResourceTriggers('admin/polls', PollAdminController::class);
        addExtraRouteResourceTriggers('admin/poll-choices', ChoiceAdminController::class);
    }
}