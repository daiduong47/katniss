@extends('admin_themes.admin_lte.master.auth')
@section('auth_type','login')
@section('box_message', trans('pages.my_settings_desc'))
@section('lib_styles')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">
@endsection
@section('lib_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
@endsection
@section('extended_scripts')
    <script>
        {!! cdataOpen() !!}
        jQuery(document).ready(function () {
            jQuery('.select2').select2();
        });
        {!! cdataClose() !!}
    </script>
@endsection
@section('auth_form')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    <form method="post">
        {!! csrf_field() !!}
        <div class="form-group">
            <label for="inputCountry">{{ trans('label.country') }}:</label>
            <select id="inputCountry" class="select2" name="country" style="width: 100%;">
                {!! countriesAsOptions($settings->country) !!}
            </select>
        </div>
        <div class="form-group">
            <label for="inputLocale">{{ trans('label.language') }}:</label>
            <select id="inputLocale" class="form-control" name="locale">
                {!! supportedLocalesAsOptions() !!}
            </select>
        </div>
        <div class="form-group">
            <label for="inputTimeZone">{{ trans('label.timezone') }}:</label>
            <select id="inputTimeZone" class="form-control select2" name="timezone" style="width: 100%;">
                {!!  timeZoneListAsOptions($settings->timezone) !!}
            </select>
        </div>
        <hr>
        <div class="form-group">
            <label for="inputCurrency">{{ trans('label.currency') }}:</label>
            <select id="inputCurrency" class="form-control" name="currency">
                {!!  currenciesAsOptions($settings->currency) !!}
            </select>
        </div>
        <div class="form-group">
            <label for="inputNumberFormat">{{ trans('label.number_format') }}:</label>
            <select id="inputNumberFormat" class="form-control" name="number_format">
                {!!  numberFormatsAsOptions($settings->number_format) !!}
            </select>
        </div>
        <hr>
        <div class="form-group">
            <label for="inputFDOW">{{ trans('label.first_day_of_week') }}:</label>
            <select id="inputFDOW" class="form-control" name="first_day_of_week">
                {!! daysOfWeekAsOptions($settings->first_day_of_week) !!}
            </select>
        </div>
        <div class="form-group">
            <label for="inputLDF">{{ trans('label.long_date_format') }}:</label>
            <select id="inputLDF" class="form-control" name="long_date_format">
                {!! longDateFormatsAsOptions($settings->long_date_format) !!}
            </select>
        </div>
        <div class="form-group">
            <label for="inputSDF">{{ trans('label.short_date_format') }}:</label>
            <select id="inputSDF" class="form-control" name="short_date_format">
                {!! shortDateFormatsAsOptions($settings->short_date_format) !!}
            </select>
        </div>
        <div class="form-group">
            <label for="inputLTF">{{ trans('label.long_time_format') }}:</label>
            <select id="inputLTF" class="form-control" name="long_time_format">
                {!! longTimeFormatsAsOptions($settings->long_time_format) !!}
            </select>
        </div>
        <div class="form-group">
            <label for="inputSTF">{{ trans('label.short_time_format') }}:</label>
            <select id="inputSTF" class="form-control" name="short_time_format">
                {!! shortTimeFormatsAsOptions($settings->short_time_format) !!}
            </select>
        </div>
        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary">{{ trans('form.action_save') }}</button>
        </div>
    </form>
    <p>
        <a href="{{ homeUrl() }}">{{ trans('label.back_to_homepage') }}</a>
        @if(!$is_auth)
            <br><a href="{{ homeUrl('auth/login') }}">{{ trans('form.action_login') }}</a>
        @else
            @if($auth_user->can('access-admin'))
                <br><a href="{{ adminUrl() }}">{{ trans('form.action_go_to') }} {{ trans('pages.admin_title') }}</a>
            @endif
        @endif
    </p>
@endsection