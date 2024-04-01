<?php
declare(strict_types=1);


namespace App\Application\Service\Handler;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseHandler
{
    /**
     * @param $data
     * @param int $statusCode
     * @return JsonResponse
     */
    public function createResponse($data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse($data, $statusCode);
    }

    public function createSuccessResponse($data, $statusCode = Response::HTTP_OK, $headers = []): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'data' => $data,
        ], $statusCode, $headers);
    }

    public function createErrorResponse($message, $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, $headers = []): Response
    {
        return new JsonResponse([
            'success' => false,
            'error' => $message,
        ], $statusCode, $headers);
    }
}