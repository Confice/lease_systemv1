<?php

namespace App\Http\Traits;

trait ApiResponse
{
    /**
     * Return a successful JSON response
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data = null, string $message = 'Success', int $code = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Return an error JSON response
     *
     * @param string $message
     * @param int $code
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message = 'An error occurred', int $code = 500, array $errors = [])
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a validation error JSON response
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validationErrorResponse($validator)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], 422);
    }
}

