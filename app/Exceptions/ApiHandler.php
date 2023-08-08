<?php

namespace App\Exceptions;

use App\Helpers\RequestResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Response;

trait ApiHandler
{

    protected function getJsonException(\Throwable $e): JsonResponse
    {
        if ($e instanceof ModelNotFoundException) {
            return $this->notFoundException();
        }

        if ($e instanceof ValidationException) {
            return $this->validationException($e);
        }

        if ($e instanceof AuthenticationException) {
            return $this->authenticationException($e);
        }

        if ($e instanceof TokenBlacklistedException) {
            return $this->authenticationException($e);
        }

        if ($e instanceof AuthorizationException) {
            return $this->authorizationException($e);
        }

        if ($e instanceof HttpException) {
            return $this->httpException($e);
        }

        return $this->genericException($e);
    }

    protected function notFoundException(): JsonResponse
    {
        return RequestResponse::error('Recurso não encontroado', ['not_found_error'], Response::HTTP_NOT_FOUND);
    }

    protected function validationException(ValidationException $e): JsonResponse
    {
        return RequestResponse::error($e->getMessage(), [$e->errors()], Response::HTTP_BAD_REQUEST);
    }

    protected function authenticationException(
        AuthenticationException|TokenBlacklistedException $e
    ): JsonResponse {
        return RequestResponse::error($e->getMessage(), ['message' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
    }

    protected function authorizationException(
        AuthorizationException $e
    ): JsonResponse {
        return RequestResponse::error($e->getMessage(), ['message' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
    }

    protected function httpException(HttpException $e): JsonResponse
    {
        return RequestResponse::error($e->getMessage(), ['message' => $e->getMessage()], $e->getStatusCode());
    }

    protected function genericException(\Throwable $e): JsonResponse
    {
        return RequestResponse::error('Internal Server Error', ['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
