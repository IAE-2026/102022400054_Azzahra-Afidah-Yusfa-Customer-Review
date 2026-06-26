<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OAT;

#[OAT\Info(
    version: "1.0.0",
    description: "API untuk mengelola review produk",
    title: "Customer Review Service API"
)]
#[OAT\SecurityScheme(
    securityScheme: "ApiKeyAuth",
    type: "apiKey",
    name: "X-IAE-KEY",
    in: "header"
)]
abstract class Controller
{
    //
}