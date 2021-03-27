<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse as JsonResponseResult;
use League\Fractal\TransformerAbstract;

trait JsonResponse
{
    use Transformer;

    /**
     * @param $response
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponseResult
     */
    protected function sendResponse($response, int $statusCode = 200, array $headers = [])
    {
        return response()->json($response, !empty($statusCode) ? $statusCode : 400, $headers);
    }

    /**
     * @param string $message
     * @return JsonResponseResult
     */
    public function sendSuccessResponse(string $message = '')
    {
        if ($message === '') {
            $message = 'Success';
        }
        return $this->sendResponse(['message' => $message], 200);
    }

    /**
     * @return JsonResponseResult
     */
    public function sendUnauthorizedResponse()
    {
        return $this->sendErrorResponse('Unauthorized', 401);
    }

    /**
     * @return JsonResponseResult
     */
    public function sendForbiddenResponse()
    {
        return $this->sendErrorResponse('Forbidden', 403);
    }

    /**
     * @param string $message
     * @return JsonResponseResult
     */
    public function sendNotFoundResponse(string $message = '')
    {
        if ($message === '') {
            $message = 'The requested resource was not found';
        }
        return $this->sendErrorResponse($message, 404);
    }

    /**
     * @param string $error
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponseResult
     */
    protected function sendErrorResponse(string $error, int $statusCode = 400, array $headers = [])
    {
        return $this->sendResponse(['error' => $error], !empty($statusCode) ? $statusCode : 400, $headers);
    }

    /**
     * @param array $errors
     * @param int $statusCode
     * @param string|null $mainError
     * @param array $headers
     * @return JsonResponseResult
     */
    protected function sendErrorsResponse(array $errors, int $statusCode = 400, ?string $mainError = null, array $headers = [])
    {
        return $this->sendResponse([
            'error' => $mainError ?? reset($errors),
            'messages' => array_values($errors)
        ], $statusCode, $headers);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    protected function sendNoContentResponse(): \Illuminate\Http\Response
    {
        return response(NULL, 204);
    }

    /**
     * @param array|Model $item
     * @param TransformerAbstract $transformer
     * @param array|null $with
     * @param null|string $resourceKey
     * @return JsonResponseResult
     */
    protected function sendTransformedItem($item, TransformerAbstract $transformer, ?array $with = null, ?string $resourceKey = null): JsonResponseResult
    {
        return $this->sendResponse($this->transformItem($item, $transformer, $with, $resourceKey));
    }

    /**
     * @param mixed $collection
     * @param TransformerAbstract $transformer
     * @param array|null $with
     * @param string $resourceKey
     * @return JsonResponseResult
     */
    protected function sendTransformedCollection(
        $collection,
        TransformerAbstract $transformer,
        ?array $with = null,
        string $resourceKey = 'data'
    ): JsonResponseResult {
        return $this->sendResponse($this->transformCollection($collection, $transformer, $with, $resourceKey));
    }
}
