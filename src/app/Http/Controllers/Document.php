<?php

namespace App\Http\Controllers;

use App\Helpers\File;
use App\Http\Requests\Calculate;
use App\Services\Document as DocumentService;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Traits\JsonResponse as JsonResponseTrait;

class Document extends Controller
{
    use JsonResponseTrait;

    /** @var DocumentService */
    protected $service;

    public function __construct(DocumentService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function calculation(Request $request)
    {
        $data = $this->validate($request, Calculate::rules());

        $spreadsheet = IOFactory::load($data['file']->getRealPath());
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        try {
            $data['csvData'] = File::formatForUserImport($sheetData, DocumentService::DOCUMENT_UPLOAD_MAPPING);
            return $this->sendResponse(['data' => $this->service->calculate($data)]);
        } catch (\Throwable $exception) {
            return $this->sendErrorResponse($exception->getMessage());
        }
    }
}