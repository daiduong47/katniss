<?php

namespace Katniss\Everdeen\Http\Controllers;

use Barryvdh\Elfinder\Connector;
use Katniss\Everdeen\Http\Request;
use Katniss\Everdeen\Utils\DateTimeHelper;

class DocumentController extends ViewController
{
    public function onlyMimes($customType)
    {
        $onlyMimes = [];

        if ($customType == 'images') {
            $onlyMimes = [
                'image/jpeg',
                'image/png',
                'image/gif',
            ];
        } elseif ($customType == 'flash') {
            $onlyMimes = [
                'application/x-shockwave-flash',
            ];
        } elseif ($customType == 'audio') {
            $onlyMimes = [
                'audio/aac',
                'audio/mp4',
                'audio/mpeg',
                'audio/ogg',
                'audio/webm',
            ];
        }

        return $onlyMimes;
    }

    public function forCkeditor(Request $request)
    {
        $this->_title(trans('pages.my_documents_title'));
        $this->_description(trans('pages.my_documents_desc'));

        $customType = $request->input('custom_type', '');
        $onlyMimes = $this->onlyMimes($customType);

        return view('file_manager.for_ckeditor', [
            'dateFormat' => DateTimeHelper::shortDateFormat(),
            'timeFormat' => DateTimeHelper::shortTimeFormat(),
            'custom_type' => $customType,
            'onlyMimes' => implode(',', $onlyMimes),
        ]);
    }

    public function forPopup(Request $request, $input_id)
    {
        $this->_title(trans('pages.my_documents_title'));
        $this->_description(trans('pages.my_documents_desc'));

        $customType = $request->input('custom_type', '');
        $onlyMimes = $this->onlyMimes($customType);

        return view('file_manager.for_popup', [
            'input_id' => $input_id,
            'dateFormat' => DateTimeHelper::shortDateFormat(),
            'timeFormat' => DateTimeHelper::shortTimeFormat(),
            'custom_type' => $customType,
            'onlyMimes' => implode(',', $onlyMimes),
        ]);
    }

    public function getConnector(Request $request)
    {
        $own_directory = $request->authUser->ownDirectory;
        $uploadAllow = [
            'image/jpeg',
            'image/png',
            'image/gif',

            'image/vnd.adobe.photoshop',
            'application/pdf',
            'application/x-shockwave-flash',

            'video/x-flv',
            'video/h264',
            'video/webm',
            'audio/aac',
            'audio/mp4',
            'audio/mpeg',
            'audio/ogg',
            'audio/webm',

            'image/vnd.djvu',
            'application/vnd.oasis.opendocument.chart',
            'application/vnd.oasis.opendocument.formula',
            'application/vnd.oasis.opendocument.graphics',
            'application/vnd.oasis.opendocument.image',
            'application/vnd.oasis.opendocument.presentation',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.oasis.opendocument.text',
            'application/epub+zip',
            'application/vnd.ms-htmlhelp',
            'application/x-mobipocket-ebook',

            'application/onenote',
            'application/vnd.ms-project',
            'application/x-mspublisher',
            'application/vnd.visio',
            'application/x-mswrite',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow',

            'application/vnd.sun.xml.calc',
            'application/vnd.sun.xml.draw',
            'application/vnd.sun.xml.impress',
            'application/vnd.sun.xml.math',

            'text/plain',

            'application/zip',
            'application/x-rar-compressed',
        ];
        $customType = $request->input('custom_type', '');
        $onlyMimes = $this->onlyMimes($customType);
        if (!empty($onlyMimes)) {
            $uploadAllow = $onlyMimes;
        }
        $connector = new Connector(new \elFinder([
            'roots' => [
                [
                    'driver' => 'LocalFileSystem',
                    'path' => userPublicPath($own_directory),
                    'alias' => 'Online drive: \Home',
                    'URL' => asset('files/' . $own_directory . '/'),
                    'accessControl' => config('elfinder.access'),
                    'dateFormat' => DateTimeHelper::shortDateFormat(),
                    'timeFormat' => DateTimeHelper::shortTimeFormat(),
                    'uploadAllow' => $uploadAllow,
                    'uploadDeny' => ['all'],
                    'uploadOrder' => ['deny', 'allow'],
                ],
            ]
        ]));
        $connector->run();
        return $connector->getResponse();
    }
}
