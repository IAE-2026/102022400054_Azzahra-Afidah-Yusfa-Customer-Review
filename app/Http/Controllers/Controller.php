<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OAT;

#[OAT\Info(
    version: "1.0.0",
    description: "API untuk mengelola review produk pelanggan",
    title: "Customer Review Service API"
)]
#[OAT\SecurityScheme(
    securityScheme: "ApiKeyAuth",
    type: "apiKey",
    name: "X-IAE-KEY",
    in: "header"
)]
#[OAT\Server(url: "http://localhost:8000", description: "Local Docker Server")]
abstract class Controller
{
    //
}