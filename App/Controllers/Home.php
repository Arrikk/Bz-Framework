<?php
namespace App\Controllers;

use Core\Controller;
use Core\Http\Req;
use Core\Http\Res;

class Home extends Controller
{
    public function index()
    {
        Res::send('
        <pre> 
        ---
        title: ARUKU API
        slug: Web/API/ARUKU_API
        page-type: web-api-overview
        tags:
          - API
          - ARUKU API
          - Steps
          - Walk
          - Experimental
          - Active
          - Points
        ---
        </pre>
        ');
    }


    public function get()
    {
      
    }
}