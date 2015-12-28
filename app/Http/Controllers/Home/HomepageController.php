<?php

namespace Katniss\Http\Controllers\Home;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Session;
use Katniss\Http\Requests;
use Katniss\Http\Controllers\ViewController;
use Katniss\Models\Helpers\DateTimeHelper;
use Katniss\Models\Helpers\ExtraActions\CallableObject;
use Katniss\Models\Helpers\Menu;
use Katniss\Models\Helpers\MenuItem;
use Katniss\Models\Helpers\NumberFormatHelper;
use Katniss\Models\UserSession;

class HomepageController extends ViewController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        add_filter('main_menu', new CallableObject(function (Menu $menu) {
            $menu->addItem(new MenuItem(
                '#social-sharing',
                'Social Sharing', 'li', null, 'page-scroll'
            ));
            $menu->addItem(new MenuItem(
                '#facebook-comment',
                'Facebook Comment', 'li', null, 'page-scroll'
            ));
            $menu->addItem(new MenuItem(
                '#example-widgets',
                'Example Widgets', 'li', null, 'page-scroll'
            ));
            $menu->addItem(new MenuItem(
                '#my-settings',
                trans('pages.my_settings_title'), 'li', null, 'page-scroll'
            ));
            $menu->addItem(new MenuItem(
                '#my-messages',
                trans('pages.my_messages_title'), 'li', null, 'page-scroll'
            ));
            return $menu;
        }));
        $settings = settings();
        $datetimeHelper = DateTimeHelper::getInstance();
        $localeCode = $settings->getLocale();
        $locale = allLocale($localeCode);
        $countryCode = $settings->getCountry();
        $country = allCountry($countryCode);

        $userSessions = UserSession::all();

        return view($this->themePage('home'), [
            'country' => $countryCode . ' - ' . $country['name'] . ' (+' . $country['calling_code'] . ')',
            'locale' => $localeCode . '_' . $locale['country_code'] . ' - ' . $locale['name'] . ' (' . $locale['native'] . ')',
            'timezone' => $settings->getTimezone() . ' (' . $datetimeHelper->getCurrentTimeZone() . ')',
            'price' => NumberFormatHelper::getInstance()->formatCurrency(12345.67),
            'long_datetime' => $datetimeHelper->compound(DateTimeHelper::LONG_DATE_FUNCTION, ' ', DateTimeHelper::LONG_TIME_FUNCTION),
            'short_datetime' => $datetimeHelper->compound(),
            'user_sessions' => $userSessions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
