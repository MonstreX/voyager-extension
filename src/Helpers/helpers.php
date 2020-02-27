<?php

/*
 *  Return JSON response with Success Code
 */
if (!function_exists('json_response_with_success')) {
    function json_response_with_success($status, $message, $data = null)
    {
        return response()->json([
            'data' => [
                'status' => $status,
                'message' => $message,
                'data' => $data,
            ],
        ]);
    }
}

/*
 *  Return JSON response with Error Code
 */
if (!function_exists('json_response_with_error')) {
    function json_response_with_error($status, Exception $error)
    {
        $code = $status;

        $message = __('voyager::generic.internal_error');

        if ($error->getCode()) {
            $code = $error->getCode();
        }

        if ($error->getMessage()) {
            $message = $error->getMessage();
        }

        return response()->json([
            'data' => [
                'status' => $code,
                'message' => $message,
            ],
        ], $code);
    }
}

/*
 *  Returns required ASSET file (css, js)
 */
if (!function_exists('voyager_extension_asset')) {
    function voyager_extension_asset($path, $secure = null)
    {
        return route('voyager.voyager_extension_assets').'?path='.urlencode($path);
    }
}
