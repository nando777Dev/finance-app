<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

class TestController extends Controller
{
    #[OA\Get(
        path: '/api/test',
        summary: 'Test endpoint',
        responses: [
            new OA\Response(response: 200, description: 'API is working'),
        ]
    )]
    public function index()
    {
        return response()->json([
            'status' => 'ok',
            'msg' => 'API funcionando alterado',
        ]);
    }
}
