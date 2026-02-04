<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Energy extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Energy & Climate - UIGM POLBAN',
            'user' => session()->get('user')
        ];

        return view('admin_pusat/energy/index', $data);
    }

    public function laporan()
    {
        $data = [
            'title' => 'Laporan Energy & Climate - UIGM POLBAN',
            'user' => session()->get('user')
        ];

        return view('admin_pusat/energy/laporan', $data);
    }
}
