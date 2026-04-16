<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Reference extends BaseController
{
    public function index()
    {
        // Check if user is logged in and has admin role
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $user = session()->get('user');
        if (!in_array($user['role'], ['admin_pusat', 'super_admin'])) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'title' => 'Manajemen Referensi Kendaraan',
            'user' => $user,
            'categories' => $this->getCategories(),
            'fuels' => $this->getFuels()
        ];

        return view('admin_pusat/reference', $data);
    }

    public function addCategory()
    {
        if (!$this->request->getMethod() === 'POST') {
            return redirect()->back();
        }

        $nama_kategori = $this->request->getPost('nama_kategori');
        
        if (empty($nama_kategori)) {
            session()->setFlashdata('error', 'Nama kategori tidak boleh kosong');
            return redirect()->back();
        }

        // Here you would typically save to database
        // For now, we'll just show success message
        session()->setFlashdata('success', 'Kategori kendaraan berhasil ditambahkan');
        return redirect()->to('/admin-pusat/reference');
    }

    public function addFuel()
    {
        if (!$this->request->getMethod() === 'POST') {
            return redirect()->back();
        }

        $nama_bahan_bakar = $this->request->getPost('nama_bahan_bakar');
        
        if (empty($nama_bahan_bakar)) {
            session()->setFlashdata('error', 'Nama bahan bakar tidak boleh kosong');
            return redirect()->back();
        }

        // Here you would typically save to database
        // For now, we'll just show success message
        session()->setFlashdata('success', 'Jenis bahan bakar berhasil ditambahkan');
        return redirect()->to('/admin-pusat/reference');
    }

    public function deleteCategory($id)
    {
        // Here you would typically delete from database
        session()->setFlashdata('success', 'Kategori kendaraan berhasil dihapus');
        return redirect()->to('/admin-pusat/reference');
    }

    public function deleteFuel($id)
    {
        // Here you would typically delete from database
        session()->setFlashdata('success', 'Jenis bahan bakar berhasil dihapus');
        return redirect()->to('/admin-pusat/reference');
    }

    private function getCategories()
    {
        // Dummy data - replace with actual database query
        return [
            ['id' => 1, 'nama_kategori' => 'Roda Dua'],
            ['id' => 2, 'nama_kategori' => 'Roda Empat'],
            ['id' => 3, 'nama_kategori' => 'Sepeda'],
            ['id' => 4, 'nama_kategori' => 'Kendaraan Umum']
        ];
    }

    private function getFuels()
    {
        // Dummy data - replace with actual database query
        return [
            ['id' => 1, 'nama_bahan_bakar' => 'Bensin'],
            ['id' => 2, 'nama_bahan_bakar' => 'Diesel'],
            ['id' => 3, 'nama_bahan_bakar' => 'Listrik'],
            ['id' => 4, 'nama_bahan_bakar' => 'Non-BBM']
        ];
    }
}