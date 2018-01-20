<?php

declare(strict_types=1);

namespace Cortex\Pages\Http\Controllers\Adminarea;

use Rinvex\Pages\Contracts\PageContract;
use Illuminate\Foundation\Http\FormRequest;
use Cortex\Foundation\DataTables\LogsDataTable;
use Cortex\Foundation\DataTables\MediaDataTable;
use Cortex\Pages\DataTables\Adminarea\PagesDataTable;
use Cortex\Pages\Http\Requests\Adminarea\PageFormRequest;
use Cortex\Foundation\Http\Controllers\AuthorizedController;

class PagesController extends AuthorizedController
{
    /**
     * {@inheritdoc}
     */
    protected $resource = 'pages';

    /**
     * Display a listing of the resource.
     *
     * @param \Cortex\Pages\DataTables\Adminarea\PagesDataTable $pagesDataTable
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(PagesDataTable $pagesDataTable)
    {
        return $pagesDataTable->with([
            'id' => 'adminarea-pages-index-table',
            'phrase' => trans('cortex/pages::common.pages'),
        ])->render('cortex/foundation::adminarea.pages.datatable');
    }

    /**
     * Get a listing of the resource logs.
     *
     * @param \Rinvex\Pages\Contracts\PageContract $page
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function logs(PageContract $page)
    {
        return request()->ajax() && request()->wantsJson()
            ? app(LogsDataTable::class)->with(['resource' => $page])->ajax()
            : intend(['url' => route('adminarea.pages.edit', ['page' => $page]).'#logs-tab']);
    }

    /**
     * Show the form for create/update of the given resource.
     *
     * @param \Rinvex\Pages\Contracts\PageContract $page
     *
     * @return \Illuminate\View\View
     */
    public function form(PageContract $page)
    {
        $logs = app(LogsDataTable::class)->with(['id' => "adminarea-pages-{$page->getKey()}-logs-table"])->html()->minifiedAjax(route('adminarea.pages.logs', ['page' => $page]));
        $media = app(MediaDataTable::class)->with(['id' => "adminarea-pages-{$page->getKey()}-media-table"])->html()->minifiedAjax(route('adminarea.pages.media.index', ['page' => $page]));

        return view('cortex/pages::adminarea.pages.page', compact('page', 'logs', 'media'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Cortex\Pages\Http\Requests\Adminarea\PageFormRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(PageFormRequest $request)
    {
        return $this->process($request, app('rinvex.pages.page'));
    }

    /**
     * Update the given resource in storage.
     *
     * @param \Cortex\Pages\Http\Requests\Adminarea\PageFormRequest $request
     * @param \Rinvex\Pages\Contracts\PageContract                  $page
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(PageFormRequest $request, PageContract $page)
    {
        return $this->process($request, $page);
    }

    /**
     * Process the form for store/update of the given resource.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param \Rinvex\Pages\Contracts\PageContract    $page
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function process(FormRequest $request, PageContract $page)
    {
        // Prepare required input fields
        $data = $request->validated();

        // Verify existing view
        if (! view()->exists($data['view'])) {
            return intend([
                'back' => true,
                'withInput' => $request->all(),
                'withErrors' => ['view' => trans('cortex/pages::messages.page.invalid_view')],
            ]);
        }

        // Save page
        $page->fill($data)->save();

        return intend([
            'url' => route('adminarea.pages.index'),
            'with' => ['success' => trans('cortex/pages::messages.page.saved', ['slug' => $page->slug])],
        ]);
    }

    /**
     * Delete the given resource from storage.
     *
     * @param \Rinvex\Pages\Contracts\PageContract $page
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delete(PageContract $page)
    {
        $page->delete();

        return intend([
            'url' => route('adminarea.pages.index'),
            'with' => ['warning' => trans('cortex/pages::messages.page.deleted', ['slug' => $page->slug])],
        ]);
    }
}
