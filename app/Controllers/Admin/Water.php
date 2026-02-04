<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Water extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Water Management - UIGM POLBAN',
            'user' => session()->get('user')
        ];

        return view('admin_pusat/water/index', $data);
    }

    public function laporan()
    {
        $data = [
            'title' => 'Laporan Water Management - UIGM POLBAN',
            'user' => session()->get('user')
        ];

        return view('admin_pusat/water/laporan', $data);
    }
}
