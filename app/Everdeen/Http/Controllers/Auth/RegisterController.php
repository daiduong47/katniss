<?php

namespace Katniss\Everdeen\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Katniss\Everdeen\Events\UserAfterRegistered;
use Katniss\Everdeen\Http\Controllers\ViewController;
use Katniss\Everdeen\Utils\MailHelper;
use Katniss\Everdeen\Models\Role;
use Katniss\Everdeen\Models\User;
use Katniss\Everdeen\Models\UserSocial;
use Validator;

class RegisterController extends ViewController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->redirectTo = homePath('auth/inactive');

        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @param boolean $fromSocial
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, $fromSocial = false)
    {
        $rules = [
            'display_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'name' => 'required|max:255|unique:users,name',
            'password' => 'required|confirmed|min:6',
        ];
        if ($fromSocial) {
            $rules['provider'] = 'required';
            $rules['provider_id'] = 'required';
            $rules['url_avatar'] = 'required|url';
        }
        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @param boolean $fromSocial
     * @return User|boolean
     */
    protected function create(array $data, $fromSocial = false)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'display_name' => $data['display_name'],
                'email' => $data['email'],
                'name' => $data['name'],
                'password' => bcrypt($data['password']),
                'url_avatar' => $data['url_avatar'],
                'url_avatar_thumb' => $data['url_avatar'],
                'activation_code' => str_random(32),
            ]);
            $defaultRole = Role::where('name', 'user')->firstOrFail();
            $user->attachRole($defaultRole->id);

            if ($fromSocial) {
                $user->socialProviders()->save(new UserSocial([
                    'provider' => $data['provider'],
                    'provider_id' => $data['provider_id'],
                ]));
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            return false;
        }

        return $user;
    }

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $this->theme->title(trans('pages.account_register_title'));
        $this->theme->description(trans('pages.account_register_desc'));

        return view($this->themePage('auth.register'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        $errorRdr = redirect(homeUrl('auth/register'))->withInput();

        if ($validator->fails()) {
            return $errorRdr->withErrors($validator);
        }

        $storedUser = $this->create($request->all());
        if ($storedUser) {
            event(new UserAfterRegistered($storedUser, $request->input('password'), false,
                array_merge($this->globalViewParams, [
                    MailHelper::EMAIL_SUBJECT => trans('label.welcome_to_') . appName(),
                    MailHelper::EMAIL_TO => $storedUser->email,
                    MailHelper::EMAIL_TO_NAME => $storedUser->display_name,
                ])
            ));

            $this->guard()->login($storedUser);
        } else {
            return $errorRdr->withErrors([trans('auth.register_failed_system_error')]);
        }

        return redirect($this->redirectPath());
    }

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showSocialRegistrationForm(Request $request)
    {
        $this->theme->title(trans('pages.account_register_title'));
        $this->theme->description(trans('pages.account_register_desc'));

        return view($this->themePage('auth.register_social'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function socialRegister(Request $request)
    {
        $validator = $this->validator($request->all(), true);

        $errorRdr = redirect(homeUrl('auth/register/social'))->withInput();

        if ($validator->fails()) {
            return $errorRdr->withErrors($validator);
        }

        $storedUser = $this->create($request->all(), true);
        if ($storedUser) {
            event(new UserAfterRegistered($storedUser, $request->input('password'), true,
                array_merge($this->globalViewParams, [
                    MailHelper::EMAIL_SUBJECT => trans('label.welcome_to_') . appName(),
                    MailHelper::EMAIL_TO => $storedUser->email,
                    MailHelper::EMAIL_TO_NAME => $storedUser->display_name,

                    'provider' => ucfirst($request->input('provider')),
                    'provider_id' => $request->input('provider_id'),
                ])
            ));

            $this->guard()->login($storedUser);
        } else {
            return $errorRdr->withErrors([trans('auth.register_failed_system_error')]);
        }

        return redirect($this->redirectPath());
    }
}