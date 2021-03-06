<?php

namespace Katniss\Everdeen\Http\Controllers\Api\V1;

use Katniss\Everdeen\Http\Controllers\ApiController;
use Katniss\Everdeen\Http\Request;
use Katniss\Everdeen\Utils\Storage\StorePhotoByCropperJs;

class UploadController extends ApiController
{
    /**
     * Upload image using JsCropper
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function useJsCropper(Request $request)
    {
        try {
            $store = new StorePhotoByCropperJs($request->file('cropper_image_file')->getRealPath(), $request->input('cropper_image_data'));
            return $this->responseSuccess([
                'store_path' => $store->getTargetFileRelativePath()
            ]);
        } catch (\Exception $ex) {
            return $this->responseFail($ex->getMessage());
        }
    }
}
