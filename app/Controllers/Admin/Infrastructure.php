<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Infrastructure extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Setting & Infrastructure - UIGM POLBAN',
            'user' => session()->get('user')
        ];

        return view('admin_pusat/infrastructure/index', $data);
    }

    public function laporan()
    {
        $data = [
            'title' => 'Laporan Infrastructure - UIGM POLBAN',
            'user' => session()->get('user')
        ];

        return view('admin_pusat/infrastructure/laporan', $data);
    }
}
