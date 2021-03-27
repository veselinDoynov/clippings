<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class Handler extends ExceptionHandler
{
    const ERROR_CODE_DB_QUERY_UNIQUE_CONSTRAINT_VIOLATION = 1062;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        TokenBlacklistedException::class,
        TokenExpiredException::class
    ];

    /**
     * @param Request $request
     * @param \Throwable $exception
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function render($request, \Throwable $exception)
    {
        if ($request instanceof Request && !$request->expectsJson() && $request->header('Accept') != 'application/vnd.ms-excel') {
            return parent::render($request, $exception);
        }

        if ($exception instanceof AuthorizationException) {
            return response()->json(['error' => 'Insufficient privileges to perform this action'], 401);
        }
        if ($exception instanceof AuthenticationException || $exception instanceof UnauthorizedHttpException) {
            return response()->json(['error' => 'Unauthorized: Access is denied due to invalid credentials. Please try: Refreshing the page or Clearing cache or opening in an incognito tab'], 401);
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json((['error' => 'Method Not Allowed']), 405);
        }
        if ($exception instanceof NotFoundHttpException) {
            return response()->json(['error' => 'The requested resource was not found. Please refresh your browser and try again.'], 404);
        }

        if ($exception instanceof TokenBlacklistedException) {
            return response()->json(['error' => 'Authorization failed due to disallowed token. Please refresh your browser and try again.'], 401);
        }

        if ($exception instanceof TokenExpiredException) {
            return response()->json(['error' => $exception->getMessage()], 401);
        }

        if ($exception instanceof ValidationException) {
            return $this->validationExceptionResponse($exception);
        }
        if ($exception instanceof ModelNotFoundException) {
            return $this->modelNotFoundResponse($exception);
        }
        if ($exception instanceof QueryException) {
            $errorCode = $exception->errorInfo[1];
            if ($errorCode == self::ERROR_CODE_DB_QUERY_UNIQUE_CONSTRAINT_VIOLATION) {
                return response()->json([
                    'error' => "Entry cannot be created because already exists"
                ], 400);
            }
        }

        if (!$this->isDebugMode()) {
            return response()->json(['error' => $exception->getMessage()], !empty($exception->getCode()) ? $exception->getCode() : 500);
        }
        return response()->json([
            'error' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'class' => get_class($exception)
        ], !empty($exception->getCode()) ? $exception->getCode() : 500);
    }

    /**
     * @param ModelNotFoundException $exception
     * @return JsonResponse
     */
    private function modelNotFoundResponse(ModelNotFoundException $exception): JsonResponse
    {
        if (empty($exception->getMessage()) || Str::startsWith($exception->getMessage(), 'No query results for model')) {
            $modelName = class_basename($exception->getModel());
            $message = sprintf("No results for %s", $modelName);
            if (!empty($exception->getIds())) {
                $idsString = implode(', ', (array)$exception->getIds());
                $modelKey = App::make($exception->getModel())->getKeyName();
                $message .= sprintf(" with {$modelKey} %s", $idsString);
            }
        } else {
            $message = $exception->getMessage();
        }
        return response()->json([
            'error' => 'The requested entity was not found',
            'messages' => [$message]
        ], 404);
    }

    /**
     * @param ValidationException $exception
     * @return JsonResponse
     */
    private function validationExceptionResponse(ValidationException $exception): JsonResponse
    {
        $errors = array_map(function ($message): string {
            return is_array($message) ? reset($message) : $message;
        }, $exception->errors());
        return response()->json(['error' => 'Invalid fields', 'messages' => $errors], 400);
    }

    /**
     * Determine if the application is in debug mode.
     *
     * @return Boolean
     */
    public function isDebugMode()
    {
        return (boolean)env('APP_DEBUG');
    }
}
