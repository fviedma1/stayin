<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NavigationController extends Controller
{
    /**
     * Obtener los items de navegación dependiendo del rol del usuario.
     *
     * @return array
     */
    public function getMenu()
    {
        
        $user = Auth::user();
        $roleId = $user->role->id ?? null;
        $hotelId = $user->hotel->id ?? null;
        $menu = [];
        $logout = ['label' => 'Logout', 'icon' => 'fas fa-sign-out-alt', 'url' => '/logout', 'active' => request()->is('logout')];

        switch ($roleId) {
            case 1: // Admin
                $menu = [
                    ['label' => 'Inici', 'icon' => 'fas fa-home', 'url' => '/hotel', 'active' => request()->is('hotel')],
                    ['label' => 'Crear', 'icon' => 'fas fa-wrench', 'url' => '/hotel/create', 'active' => request()->is('hotel/create')],
                    ['label' => 'Noticies', 'icon' => 'fas fa-newspaper', 'url' => '/hotel/news', 'active' => request()->is('/hotel/news')],
                    $logout,
                ];
                break;

            case 2: // Recepcionista
                $menu = [
                    ['label' => 'Gestor', 'icon' => 'fas fa-calendar', 'url' => "/hotel/$hotelId/gestor", 'active' => request()->is("hotel/$hotelId/gestor")],
                    ['label' => 'Reserves Pendents', 'icon' => 'fas fa-hourglass-half', 'url' => "/hotel/$hotelId/reserves", 'active' => request()->is("hotel/$hotelId/reserves")],
                    $logout,
                ];
                break;

            case 3: // Customer
                $menu = [
                    ['label' => 'Habitacions', 'icon' => 'fas fa-bed', 'url' => '/hotel/room', 'active' => request()->is('hotel/room')],
                    ['label' => 'Gestor', 'icon' => 'fas fa-calendar', 'url' => '/hotel/gestor', 'active' => request()->is('hotel/gestor')],
                    ['label' => 'Reserves Pendets', 'icon' => 'fas fa-search', 'url' => '/hotel/reserves', 'active' => request()->is('hotel/reserves')],
                    $logout,
                ];
                break;

            default: // Usuario no autenticado o sin rol
                $menu = [
                    ['label' => 'Inici', 'icon' => 'fas fa-home', 'url' => '/', 'active' => request()->is('/')],
                    ['label' => 'Hotels', 'icon' => 'fas fa-hotel', 'url' => '/hotel/show', 'active' => request()->is('hotel/show')],
                    ['label' => 'Crear', 'icon' => 'fas fa-wrench', 'url' => '/hotel/create', 'active' => request()->is('hotel/create')],
                    ['label' => 'Gestor', 'icon' => 'fas fa-calendar', 'url' => '/hotel/gestor', 'active' => request()->is('hotel/gestor')],
                    $logout,
                ];
                break;
        }

        return $menu;
    }
}
