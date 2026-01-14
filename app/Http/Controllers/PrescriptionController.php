<?php
namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Prescription $prescription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prescription $prescription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prescription $prescription)
    {
        try {
            if (! $prescription) {
                return response()->json([
                    'success' => false,
                    'msg'     => 'Data tidak ditemukan!',
                ], 404);
            }

            $prescription->update([
                'pharmacist_id' => Auth::id(),
                'status'        => 'paid',
            ]);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($prescription)
                ->event('updated')
                ->log('Resep sudah berhasil dibayar');

            return response()->json([
                'success' => true,
                'msg'     => 'Data berhasil diperbarui!',
            ], 200);
        } catch (\Exception $e) {
            activity()
                ->causedBy(Auth::user())
                ->performedOn($prescription)
                ->event('updated')
                ->log($e->getMessage());

            return response()->json([
                'success' => false,
                'msg'     => 'Terjadi kesalahan. Coba lagi nanti',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prescription $prescription)
    {
        //
    }
}
