<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\DB;


class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendSuccess($data)
    {
        $response = [
            'success' => true,
            'data'    => $data,
            'status_code' => 200,
        ];

        return response()->json($response);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error)
    {
        $response = [
            'success' => false,
            'message' => $error,
            'status_code' => 404
        ];

        return response()->json($response);
    }
}
