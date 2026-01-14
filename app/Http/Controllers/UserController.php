<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil data role
        $getRoles = Role::all()->pluck('name');

        return view('admin.users.create', compact('getRoles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        try {
            // Simpan data user
            $createUser = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Tambahkan role sesuai dengan yang dipilih
            $createUser->assignRole($request->role);

            if ($request->role == 'doctor') {
                // Simpan file dulu kedalam variable
                $file = $request->signature;

                // Buat hasname dari file yang diupload
                $hasNameFile = $file->hashname();

                // Simpan kedalam folder doctor-signature yang ada di public
                $file->storeAs(
                    'doctor-signatures',
                    $hasNameFile,
                    'public'
                );

                // Masukan profile dokter kedalam table doctor_profiles
                $createUser->doctorProfile()->create([
                    'phone'          => $request->phone,
                    'str_number'     => $request->str_number,
                    'specialization' => $request->specialization,
                    'signature'      => $hasNameFile,
                ]);

                activity()
                    ->causedBy(Auth::user())
                    ->event('created')
                    ->log('Akun dokter telah ditambahkan oleh ' . auth()->user()->name);
            }

            if ($request->role == 'pharmacist') {
                // Masukan profile apoteker kedalam table pharmacist_profiles
                $createUser->pharmacistProfile()->create([
                    'phone'       => $request->phone,
                    'sipa_number' => $request->sipa_number,
                    'work_unit'   => $request->work_unit,
                ]);

                activity()
                    ->causedBy(Auth::user())
                    ->event('created')
                    ->log('Akun apoteker telah ditambahkan oleh ' . auth()->user()->name);
            }

            if ($request->role == 'admin') {
                activity()
                    ->causedBy(Auth::user())
                    ->event('created')
                    ->log('Akun admin telah ditambahkan oleh ' . auth()->user()->name);
            }

            alert('Berhasil', 'User berhasil ditambahkan', 'success');

            return redirect(route('admin.home'));
        } catch (\Exception $e) {
            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->log($e->getMessage());

            alert('Gagal', "Terdapat kesalahan. Coba lagi nanti", 'error');

            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Ambil data user dengan profilnya (doctor, pharmacist, dan role)
        $data = User::with(['doctorProfile', 'pharmacistProfile', 'roles'])->find($id);

        // Ambil data role
        $getRoles = Role::all()->pluck('name');

        return view('admin.users.edit', compact('data', 'getRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            // Ambil data dengan profil (doctor dan pharmacist)
            $find = User::with(['doctorProfile', 'pharmacistProfile'])->find($id);

            // Pisahkan payload menjadi dua (User dan Profile)
            $payloadUser = [
                'name'  => $request->name,
                'email' => $request->email,
            ];

            $payloadProfile = [
                'phone' => $request->phone,
            ];

            if ($request->password) {
                $payloadUser['password'] = $request->password;
            }

            // Update table User
            $find->update($payloadUser);

            if ($request->role == 'doctor') {
                // Simpan file dulu kedalam variable
                $file = $request->signature ?? null;

                // Isi sisa payload dari profile yang sesuai (dokter)
                $payloadProfile['str_number']     = $request->str_number;
                $payloadProfile['specialization'] = $request->specialization;

                if ($file) {
                    // Buat hasname dari file yang diupload
                    $hasNameFile                 = $file->hashname();
                    $payloadProfile['signature'] = $hasNameFile;

                    // Hapus signature sebelumnya terlebih dahulu
                    Storage::disk('public')->delete('doctor-signatures/' . $find->doctorProfile->signature);

                    // Simpan kedalam folder doctor-signatures yang ada di public
                    $file->storeAs(
                        'doctor-signatures',
                        $hasNameFile,
                        'public'
                    );

                    activity()
                        ->causedBy(Auth::user())
                        ->event('updated')
                        ->log('Signature telah di update oleh ' . auth()->user()->name);
                }

                // Masukan profile dokter kedalam table doctor_profiles
                $find->doctorProfile()->update($payloadProfile);

                activity()
                    ->causedBy(Auth::user())
                    ->event('updated')
                    ->log('Akun dokter telah diupdate oleh ' . auth()->user()->name);
            }

            if ($request->role == 'pharmacist') {
                // Isi sisa payload dari profile yang sesuai (apoteker / phramacist)
                $payloadProfile['sipa_number'] = $request->sipa_number;
                $payloadProfile['work_unit']   = $request->work_unit;

                // Masukan profile apoteker kedalam table pharmacist_profiles
                $find->pharmacistProfile()->update($payloadProfile);

                activity()
                    ->causedBy(Auth::user())
                    ->event('updated')
                    ->log('Akun apoteker telah diupdate oleh ' . auth()->user()->name);
            }

            if ($request->role == 'admin') {
                activity()
                    ->causedBy(Auth::user())
                    ->event('updated')
                    ->log('Akun admin telah diupdate oleh ' . auth()->user()->name);
            }

            alert('Berhasil', 'User berhasil diupdate', 'success');

            return redirect(route('admin.home'));
        } catch (\Exception $e) {
            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->log($e->getMessage());

            alert('Gagal', 'Terdapat kesalahan. Coba lagi nanti', 'error');

            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Cari data user
            $find = User::find($id);

            // Hapus data profile doctor
            if ($find->hasRole('doctor')) {
                $find->doctorProfile()->delete();

                activity()
                    ->causedBy(Auth::user())
                    ->event('deleted')
                    ->log('Akun dokter telah dihapus oleh ' . auth()->user()->name);
            }

            // Hapus data profile pharmacist
            if ($find->hasRole('pharmacist')) {
                $find->pharmacistProfile()->delete();

                activity()
                    ->causedBy(Auth::user())
                    ->event('deleted')
                    ->log('Akun apoteker telah dihapus oleh ' . auth()->user()->name);
            }

            if ($find->hasRole('admin')) {
                activity()
                    ->causedBy(Auth::user())
                    ->event('deleted')
                    ->log('Akun admin telah dihapus oleh ' . auth()->user()->name);
            }

            // Hapus data user
            $find->delete();

            alert('Berhasil', 'User behasil di hapus', 'success');

            return redirect()->back();
        } catch (\Exception $e) {
            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->log($e->getMessage());

            alert('Gagal', 'Terdapat kesalahan', 'error');

            return redirect()->back();
        }
    }
}
