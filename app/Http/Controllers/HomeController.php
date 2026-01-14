<?php
namespace App\Http\Controllers;

use App\DataTables\UserDataTable;
use App\DataTables\PatientDataTable;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(UserDataTable $dataTable)
    {
        $title = 'Hapus Akun!';
        $text = "Anda yakin akan hapus akun ini?";
        confirmDelete($title, $text);
        
        return $dataTable->render('admin.index');
    }

    public function doctorIndex(PatientDataTable $dataTable)
    {
        return $dataTable->render('doctor.index');
    }

    public function pharmacistIndex(PatientDataTable $dataTable)
    {
        return $dataTable->render('pharmacist.index');
    }
}
