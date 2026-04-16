<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LogBook extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Ambil data Program 3R dari tabel logbooks
        $data['riwayat_3r'] = $db->table('logbooks')
            ->where('kategori', '3R')
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();
        
        return view('admin_pusat/logbook/index', $data);
    }

    public function save()
    {
        $db = \Config\Database::connect();
        
        $data = [
            'kategori'        => $this->request->getPost('kategori'),
            'tanggal'         => $this->request->getPost('tanggal'),
            'sumber_sampah'   => $this->request->getPost('sumber_sampah'),
            'jenis_material'  => $this->request->getPost('jenis_material'),
            'berat_terkumpul' => $this->request->getPost('berat_terkumpul'),
            'tindakan'        => $this->request->getPost('tindakan'),
            'created_at'      => date('Y-m-d H:i:s'),
        ];

        $db->table('logbooks')->insert($data);
        
        return redirect()->to(base_url('admin-pusat/logbook'))->with('success', 'Data berhasil disimpan');
    }
}