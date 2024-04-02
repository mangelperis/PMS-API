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
        return new JsonResponse($data, $statusCode, [], true);
    }

    /**
     * @param $data
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    public function createSuccessResponse($data, int $statusCode = Response::HTTP_OK, array $headers = []): JsonResponse
    {
        return new JsonResponse([], $statusCode, $headers);
    }

    /**
     * @param $message
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    public function createErrorResponse($message, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, array $headers = []): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'error' => $message,
        ], $statusCode, $headers);
    }
}