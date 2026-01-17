<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(version: "1.0.0", title: "My API Title")]
#[OA\Server(url: 'http://localhost:8000', description: "Local Server")]
abstract class Controller
{
    //
}
