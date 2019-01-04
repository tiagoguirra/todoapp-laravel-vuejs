<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */

    public function sendResponse($message, $result = [], $code = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $result,
        ];

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */

    public function sendValidationError($validation){
        $arrayErrors = json_decode($validation,true);
        $arrayString = [];
        foreach($arrayErrors as $error){
            if(is_array($error)){
                foreach($error as $er){
                    $arrayString[] = $er;
                }
            }else{
                $arrayString[] = $error;
            }
        }
        $response = [
            'success' => false,
            'message' => 'validation errors',
            'data' => $arrayString
        ];
        return response()->json($response, 400);
    }
    public function sendError($error, $errorMessages = [], $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = is_array($errorMessages)?$errorMessages:[$errorMessages];
        }

        return response()->json($response, $code);
    }

    public function sqlError($errors)
    {
        $response = [
            'success' => false,
            'message' => 'Integrity Violation Error',
            'data' => $errors,
        ];
        
        return response()->json($response, 500);
    }
}
