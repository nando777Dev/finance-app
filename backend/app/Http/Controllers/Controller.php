<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Finance App API',
    version: '1.0.0',
    description: 'API para organização financeira',
    contact: new OA\Contact(email: 'contato@financeapp.com')
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
abstract class Controller
{
    //
}
