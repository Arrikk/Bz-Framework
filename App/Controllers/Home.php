<?php
namespace App\Controllers;

use Core\Controller;
use Core\Http\Res;

class Home extends Controller
{
    public function index()
    {
        Res::send('
        <pre> 
        ---
        title: CODEHART
        slug: Web/API/BRUIZ
        page-type: web-api-overview
        tags:
          - API
          - WEB
          - Models
          - Controlers
          - Views
          - Routes
        ---
        </pre>
        ');
    }
}