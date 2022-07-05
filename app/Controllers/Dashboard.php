<?php

namespace App\Controllers;

use App\Models\User;
use CodeIgniter\View\Table;
use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $model = new User();
        $id = session()->get('id');
        $profilData = $model->select('name, email, sekolah, nisn, wa, kota, provinsi, image, role_id')->find($id);
        dd($profilData);
    
        // return halaman profil
    }

    public function admin()
    {
        $session = session();
        $model = new User();
        $user = $model->select('id, name')->find($session->get('id'));
        // dd(route_to('auth/logout'));

        return view('layouts/main', ['title' => 'Dashboard']);
    }

    public function listUser()
    {
        $model = new User();
        $query = $model->select('users.id, users.name, email, sekolah, nisn, wa, kota, provinsi, image, bukti_nisn, bukti_bayar, user_roles.name as role')
            ->join('user_roles', 'users.role_id = user_roles.id');
        $kategori = $this->request->getGet('kategori');
        $orderBy = $this->request->getGet('orderBy') ?? 'id';
        $order = $this->request->getGet('order') ?? 'ASC';

        if ($kategori) {
            $query = $query->where('role_id', $kategori);
        }
        $query = $query->orderBy($orderBy, $order);
        $userData = $query->paginate(25);

        array_walk($userData, function (&$item)
        {
            $item['image'] = "<a role='button' class='btn btn-primary". ($item['image']?"'":"disabled' aria-disabled='true'")."' href='".($item['image']??"#'")."'>Buka</a>";
            $item['bukti_nisn'] = "<a role='button' class='btn btn-primary". ($item['bukti_nisn']?"'":"disabled' aria-disabled='true'")."' href='".($item['bukti_nisn']??"#'")."'>Buka</a>";
            $item['bukti_bayar'] = "<a role='button' class='btn btn-primary". ($item['bukti_bayar']?"'":"disabled' aria-disabled='true'")."' href='".($item['bukti_bayar']??"#'")."'>Buka</a>";
            $item['action'] = "<a role='button' class='btn btn-primary btn-sm' href='" . route_to('Admin::editProfil', $item['id']) . "'>Edit</a>"
                ."<a role='button' class='btn btn-danger btn-sm' href='" . route_to('Admin::deleteUser', $item['id']) . "'>Delete</a>";
        });

        $template = [
            'table_open'	=>	'<table class="table table-hover table-striped table-responsive">'
        ];
        $table = new Table($template);
        $table->setHeading('Id', 'Nama', 'Email', 'Sekolah', 'Nisn', 'No. Wa', 'Kota', 'Provinsi', 'Image', 'Bukti NISN', 'Bukti Bayar', 'Status', 'Action');
        
        $data = [
            'title' =>  'List User',
            'table' =>  $table->generate($userData),
            'pager' =>  $model->pager,
        ];

        return view('dashboard/list_user', $data);
    }

    public function editProfil()
    {
        $model = new User();
        $id = session()->get('id');
        $profilData = $model->select('name, email, sekolah, nisn, wa, kota, provinsi, image, bukti_nisn, role_id')->find($id);

        // return halaman edit profil
    }

    public function changePassword()
    {
        // return halaman ganti password
    }

    public function pembayaran()
    {
        // return halaman metode pembayaran
    }

    public function buktiBayar()
    {
        // return halaman upload bukti bayar
    }
}
