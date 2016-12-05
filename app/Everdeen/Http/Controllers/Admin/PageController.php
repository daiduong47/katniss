<?php

namespace Katniss\Everdeen\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Katniss\Everdeen\Exceptions\KatnissException;
use Katniss\Everdeen\Http\Controllers\ViewController;
use Katniss\Everdeen\Repositories\PageRepository;
use Katniss\Everdeen\Themes\HomeThemes\HomeThemeFacade;
use Katniss\Everdeen\Utils\PaginationHelper;
use Katniss\Everdeen\Utils\QueryStringBuilder;

class PageController extends ViewController
{
    private $pageRepository;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->viewPath = 'page';
        $this->pageRepository = new PageRepository($request->input('id'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->theme->title(trans('pages.admin_pages_title'));
        $this->theme->description(trans('pages.admin_pages_desc'));

        $pages = $this->pageRepository->getPaged();

        $query = new QueryStringBuilder([
            'page' => $pages->currentPage()
        ], adminUrl('pages'));
        return $this->_list([
            'pages' => $pages,
            'query' => $query,
            'page_helper' => new PaginationHelper($pages->lastPage(), $pages->currentPage(), $pages->perPage()),
            'rdr_param' => rdrQueryParam($request->fullUrl()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $this->theme->title([trans('pages.admin_pages_title'), trans('form.action_add')]);
        $this->theme->description(trans('pages.admin_pages_desc'));

        return $this->_add([
            'templates' => HomeThemeFacade::pageTemplates(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateResult = $this->validateMultipleLocaleInputs($request, [
            'title' => 'required|max:255',
            'slug' => 'required|max:255|unique:post_translations,slug',
            'description' => 'sometimes|max:255',
        ]);

        $error_redirect = redirect(adminUrl('pages/add'))
            ->withInput();

        if ($validateResult->isFailed()) {
            return $error_redirect->withErrors($validateResult->getFailed());
        }

        $validator = Validator::make($request->all(), [
            'featured_image' => 'sometimes|url',
        ]);
        if ($validator->fails()) {
            return $error_redirect->withErrors($validator);
        }

        try {
            $this->pageRepository->create(
                $this->authUser->id,
                $request->input('template', ''),
                $request->input('featured_image', ''),
                $validateResult->getLocalizedInputs()
            );
        } catch (KatnissException $ex) {
            return $error_redirect->withErrors([$ex->getMessage()]);
        }

        return redirect(adminUrl('pages'));
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $page = $this->pageRepository->model($id);

        $this->theme->title([trans('pages.admin_pages_title'), trans('form.action_edit')]);
        $this->theme->description(trans('pages.admin_pages_desc'));

        return $this->_edit([
            'page' => $page,
            'templates' => HomeThemeFacade::pageTemplates(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $page = $this->pageRepository->model();

        $redirect = redirect(adminUrl('pages/{id}/edit', ['id' => $page->id]));

        $validateResult = $this->validateMultipleLocaleInputs($request, [
            'title' => 'required|max:255',
            'slug' => 'required|max:255|unique:post_translations,slug,' . $page->id . ',post_id',
            'description' => 'sometimes|max:255',
        ]);

        if ($validateResult->isFailed()) {
            return $redirect->withErrors($validateResult->getFailed());
        }

        $validator = Validator::make($request->all(), [
            'featured_image' => 'sometimes|url',
        ]);
        if ($validator->fails()) {
            return $redirect->withErrors($validator);
        }

        try {
            $this->pageRepository->update(
                $this->authUser->id,
                $request->input('template', ''),
                $request->input('featured_image', ''),
                $validateResult->getLocalizedInputs()
            );
        } catch (KatnissException $ex) {
            return $redirect->withErrors([$ex->getMessage()]);
        }
        return $redirect;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $this->pageRepository->model($id);

        $this->_rdrUrl($request, adminUrl('pages'), $rdrUrl, $errorRdrUrl);

        try {
            $this->pageRepository->delete();
        } catch (KatnissException $ex) {
            return redirect($errorRdrUrl)->withErrors([$ex->getMessage()]);
        }

        return redirect($rdrUrl);
    }
}