<?php

namespace App\Views\components\app;

use Core\Component;
use Core\Http\Res;
use Core\View;

class App extends Component
{
    // public function _html($title = '')
    // {
    //     View::component('app/html', ['title' => $title]);
    // }

    // public function _topbar()
    // {
    //     View::component('app/topbar');
    // }

    public function _body($body, $page = null)
    {
        $page;
        echo '<div class="nk-content ">
        <div class="container wide-xl">
        <div class="nk-content-inner">';
        View::component('app/sidebar');
        echo '<div class="nk-content-body">';
        echo ' <div class="nk-content-wrap">';
        require_once $body;
        echo '</div>';
        View::component('app/footer');
        echo '</div>';
        echo '</div></div></div>';
    }

    // public function _htmlEnd()
    // {
    //     View::component('app/htmlend');
    // }
    
    public function _script($script = '')
    {
        if(is_readable($script)){
            require $script;
        }
    }
}
