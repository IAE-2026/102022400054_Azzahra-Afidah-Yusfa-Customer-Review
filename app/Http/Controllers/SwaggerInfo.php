<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OAT;

#[OAT\OpenApi(
    openapi: '3.0.0',
    info: new OAT\Info(
        title: 'Customer Review Service API',
        version: '1.0.0',
        description: 'API untuk mengelola review produk pelanggan'
    )
)]
#[OAT\SecurityScheme(
    securityScheme: 'ApiKeyAuth',
    type: 'apiKey',
    in: 'header',
    name: 'X-IAE-KEY'
)]
#[OAT\Server(url: 'http://localhost:8000', description: 'Local Docker Server')]
class SwaggerInfo {}