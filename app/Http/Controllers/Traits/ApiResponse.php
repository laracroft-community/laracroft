<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Log;

trait ApiResponse
{
    private function response($success, $code, $error_message = null, $errors = null, $response_data = null)
    {
        return [
            'success' => $success,
            'code' => $code,
            'body' => [
                'error_message' => $error_message,
                'errors' => $errors,
                'response_data' => $response_data,
                // 'response_data' => ["data" => $response_data],
            ]
        ];
    }

    /**
     * @param mixed $data
     */
    public function respondOk($data)
    {
        return $this->response(true, 200, null, null, $data);
    }

    /**
     * Method for converting thrown exceptions into http response
     *
     * @param \Exception $e
     * @param array|null $errors
     */
    protected function respondError(\Exception $e, $errors = null)
    {
        Log::error("Api Custom respondError ". $e->getMessage(). " ". $e->getFile(). " ". $e->getLine());
        Log::error("Api Custom respondError ".$e);
        return $this->response(false, 500, $e->getMessage(), $errors);
    }

    /**
     * Method for converting bad request to a json formatted response
     *
     * @param array $errors
     */
    protected function respondBadRequest($errors)
    {
        return $this->response(false, 422, null, $errors);
    }
}
