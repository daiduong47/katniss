<?php
/**
 * Created by PhpStorm.
 * User: Nguyen Tuan Linh
 * Date: 2016-08-18
 * Time: 23:11
 */

namespace Katniss\Http\Controllers;


use Illuminate\Http\Request;

class ApiController extends KatnissController
{
    use ApiResponseTrait;

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
}