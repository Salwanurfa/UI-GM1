<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Education extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Education & Research - UIGM POLBAN',
            'user' => session()->get('user')
        ];

        return view('admin_pusat/education/index', $data);
    }

    public function laporan()
    {
        $data = [
            'title' => 'Laporan Education & Research - UIGM POLBAN',
            'user' => session()->get('user')
        ];

        return view('admin_pusat/education/laporan', $data);
    }
}
