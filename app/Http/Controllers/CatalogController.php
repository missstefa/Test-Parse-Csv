<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogStoreRequest;
use App\Http\Services\CatalogService;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;

class CatalogController extends Controller
{
    private CatalogService $catalogService;

    public function __construct(CatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    public function store(CatalogStoreRequest $request)
    {
        $file = $request->file('file');

        $fileName = $file->getClientOriginalName();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        return Response::stream($this->catalogService->storeAndGetCsv($file), HttpResponse::HTTP_OK, $headers);
    }
}