<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Transportation extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Transportation - UIGM POLBAN',
            'user' => session()->get('user')
        ];

        return view('admin_pusat/transportation/index', $data);
    }

    public function laporan()
    {
        $data = [
            'title' => 'Laporan Transportation - UIGM POLBAN',
            'user' => session()->get('user')
        ];

        return view('admin_pusat/transportation/laporan', $data);
    }
}
