<?php
namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Models\PatientFile;
use App\Models\Prescription;
use App\Services\MedicineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    // Deklarasikan properti
    protected $medicineService;

    // Injeksi Service melalui Constructor
    public function __construct(MedicineService $medicineService)
    {
        $this->medicineService = $medicineService;
    }

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
        return view('doctor.create');
    }

    public function getMedicines()
    {
        try {
            $medicines = $this->medicineService->getMedicines(); // Service sudah mengurus Token & Cache secara internal
            return response()->json($medicines);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data obat'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request)
    {
        $validated = $request->validated();

        try {
            return DB::transaction(function () use ($request, $validated) {
                // Simpan Data Pasien
                if ($request->patient_id) {
                    $patient = Patient::find($request->patient_id);
                } else {
                    $patient = Patient::create([
                        'name'      => Str::title($validated['name']),
                        'born_date' => $validated['born_date'],
                        'sex'       => $validated['sex'],
                    ]);
                }

                // Simpan Examination
                $examTime    = now();
                $examination = $patient->examinations()->create([
                    'user_id'          => Auth::id(),
                    'examination_time' => $examTime,
                    'height'           => $validated['height'],
                    'weight'           => $validated['weight'],
                    'systole'          => $validated['systole'],
                    'diastole'         => $validated['diastole'],
                    'heart_rate'       => $validated['heart_rate'],
                    'respiration_rate' => $validated['respiration_rate'],
                    'temperature'      => $validated['temperature'],
                    'clinical_notes'   => $validated['clinical_notes'],
                ]);

                // Handle Berkas (Opsional)
                if ($request->hasFile('patient_files')) {
                    foreach ($request->file('patient_files') as $file) {
                        // Buat hasname dari file yang diupload
                        $hasNameFile = $file->hashname();

                        // Simpan kedalam folder doctor-signature yang ada di public
                        $file->storeAs('patient-files', $hasNameFile, 'public');
                        $examination->patientFiles()->create(['file' => $hasNameFile]);
                    }

                    // Catat activity log untuk penghapusan / pergantian detail resep
                    activity()
                        ->causedBy(Auth::user())
                        ->event('created')
                        ->withProperties(['attributes' => $request->patient_files])
                        ->log(count($request->patient_files) . ' berkas pasien ditambahkan oleh ' . auth()->user()->name);
                }

                // Inisialisasi Prescription (Header)
                $prescription = $examination->prescription()->create([
                    'total_price' => 0, // Akan diupdate setelah looping detail
                ]);

                $grandTotal = 0;
                $token      = Cache::get('medicine_api_token');

                // Looping Medicines untuk Detail
                // Di dalam Controller update/store
                foreach ($request->medicines as $index => $medicine) {
                    // Extract data (Gunakan explode seperti cara Anda)
                    $extractId   = explode('_', $medicine)[0];
                    $extractName = explode('_', $medicine)[1];
                    $qty         = $request->quantities[$index];

                    // Gunakan Service untuk cari harga
                    // Service Provider sudah mengurus Singleton dan Token secara otomatis
                    $unitPrice = $this->medicineService->getPriceByDate($extractId, $examTime);

                    $subTotal    = $unitPrice * $qty;
                    $grandTotal += $subTotal;

                    // Simpan ke database (Poin 5: Struktur Database)
                    $prescription->prescriptionDetails()->create([
                        'medicine_id'   => $extractId,
                        'medicine_name' => $extractName,
                        'qty'           => $qty,
                        'unit_price'    => $unitPrice,
                        'sub_total'     => $subTotal,
                    ]);
                }

                // Update Total Price di Header Prescription
                $prescription->update(['total_price' => $grandTotal]);

                alert('Berhasil', 'Data pesien berhasil disimpan', 'success');

                return redirect()->route('doctor.home');
            });
        } catch (\Exception $e) {
            alert('Gagal', "Terdapat kesalahan. Coba lagi nanti", 'error');
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $checkRoleDoctor     = auth()->user()->hasRole('doctor');
        $checkRolePharmacist = auth()->user()->hasRole('pharmacist');

        // Eager load examinations dan prescription untuk performa optimal
        $patient->load([
            'examinations' => function ($q) use ($checkRoleDoctor) {
                $q->when($checkRoleDoctor, function ($query) {
                    $query->where('user_id', auth()->id())
                        ->with('prescription');
                }, function ($query) {
                    $query->with('prescription', 'user');
                })
                    ->latest();
            },
        ]);

        if ($checkRoleDoctor) {
            return view('doctor.show', compact('patient'));
        }

        if ($checkRolePharmacist) {
            return view('pharmacist.show', compact('patient'));
        }
    }

    public function printPrescription($id)
    {
        $prescription = Prescription::with(['prescriptionDetails', 'examination.user.doctorProfile', 'examination.patient'])
            ->findOrFail($id);

        $grandTotal = $prescription->prescriptionDetails->sum('sub_total');

        // Catat activity log sebelum cetak
        activity()
            ->performedOn($prescription)
            ->causedBy(Auth::user())
            ->event('show')
            ->log(Auth::user()->name . ' membuka halaman cetak resep.');

        return view('print.prescription', compact('prescription', 'grandTotal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Patient $patient)
    {
        // Ambil data pemeriksaan dengan filter user_id dokter yang login
        // Gunakan eager loading agar performa cepat (menghindari N+1)
        $patient->load([
            'examinations' => function ($query) {
                $query->latest('examination_time')
                    ->with(['prescription.prescriptionDetails', 'patientFiles']);
            },
        ]);

        // Tentukan data mana yang mau ditampilkan di FORM
        $selectedExamId = $request->query('exam_id');

        if ($selectedExamId) {
            // Jika datang dari riwayat (ada exam_id), cari yang spesifik
            $examination = $patient->examinations->firstWhere('id', $selectedExamId);
        } else {
            // Jika datang dari DataTable (tidak ada exam_id), ambil yang paling terbaru milik dokter ini
            $examination = $patient->examinations->first();
        }

        // Jika pasien terdaftar tapi belum pernah diperiksa dokter ini, $examination akan null
        if (! $examination) {
            alert('Gagal', 'Pasien ini belum memiliki riwayat pemeriksaan dengan Anda. Silakan tambah pemeriksaan baru.', 'error');
            return redirect()->route('patients.show', $patient->id);
        }

        return view('doctor.edit', [
            'patient'    => $patient,
            'latestExam' => $examination, // Nama variabel tetap agar tidak merusak Blade
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $validated = $request->validated();

        try {
            // Kita bungkus dalam db transaction supaya jika ada error, maka semua dibatalkan
            return DB::transaction(function () use ($request, $patient) {
                // Ambil pemeriksaan terakhir dokter ini untuk pasien tersebut
                $exam = $patient
                    ->examinations()
                    ->where('user_id', auth()->id())
                    ->where('id', $request->exam_id)
                    ->latest()
                    ->firstOrFail();

                // Update data tanda vital dan catatan klinis
                $exam->update([
                    'height'         => $request->height,
                    'weight'         => $request->weight,
                    'systole'        => $request->systole,
                    'diastole'       => $request->diastole,
                    'heart_rate'     => $request->heart_rate,
                    'temperature'    => $request->temperature,
                    'clinical_notes' => $request->clinical_notes,
                ]);

                // Hapus gambar
                if ($request->has('delete_files')) {
                    // Ambil data filenya dulu dari database berdasarkan ID yang dicentang
                    $files = PatientFile::whereIn('id', $request->delete_files)->get();

                    // Kumpulkan semua path ke dalam array
                    $pathsToDelete = $files
                        ->map(function ($file) {
                            return 'patient-files/' . $file->file;
                        })
                        ->toArray();

                    // Hapus semua file fisiknya sekaligus dari folder storage
                    Storage::disk('public')->delete($pathsToDelete);

                    // Hapus data dari database
                    PatientFile::whereIn('id', $request->delete_files)->delete();

                    // Catat activity log untuk penghapusan gambar
                    activity()
                        ->causedBy(Auth::user())
                        ->withProperties(['attributes' => $request->delete_files])
                        ->event('deleted')
                        ->log(count($request->delete_files) . ' berkas pasien telah dihapus dan data berkas diarsipkan oleh ' . auth()->user()->name);
                }

                // Upload gambar baru jika ada
                if ($request->hasFile('patient_files')) {
                    foreach ($request->file('patient_files') as $file) {
                        $fileName = $file->hashname();
                        $file->storeAs('patient-files', $fileName, 'public');

                        // Simpan nama filenya ke tabel patient_files
                        $exam->patientFiles()->create([
                            'file' => $fileName,
                        ]);
                    }

                    activity()
                        ->causedBy(Auth::user())
                        ->withProperties(['attributes' => $request->patient_files])
                        ->event('created')
                        ->log(count($request->delete_files) . ' berkas pasien telah ditambah oleh ' . auth()->user()->name);
                }

                // Update resep obat (Hapus yang lama, isi yang baru)
                $prescription = $exam->prescription;
                $prescription->prescriptionDetails()->delete(); // Bersihkan detail lama dulu

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties(['attributes' => $request->medicines])
                    ->event('deleted')
                    ->log(count($request->medicines) . ' detail resep dihapus terlebih dahulu oleh ' . auth()->user()->name);

                $grandTotal = 0;
                $token      = Cache::get('medicine_api_token');

                foreach ($request->medicines as $index => $medicine) {
                    // Ambil Data & Harga Obat dari API
                    $extractId   = explode('_', $medicine)[0];
                    $extractName = explode('_', $medicine)[1];
                    $qty         = $request->quantities[$index];

                    $unitPrice = $this->medicineService->getPriceByDate($extractId, $exam->examination_time);

                    $subTotal    = $unitPrice * $qty;
                    $grandTotal += $subTotal;

                    // Masukan detail resep baru
                    $prescription->prescriptionDetails()->create([
                        'medicine_id'   => $extractId,
                        'medicine_name' => $extractName,
                        'qty'           => $qty,
                        'unit_price'    => $unitPrice,
                        'sub_total'     => $subTotal,
                    ]);
                }

                // Terakhir update total harga pada resep
                $prescription->update(['total_price' => $grandTotal]);

                alert('Berhasil', 'Data berhasil diperbarui', 'success');

                return redirect()->route('patients.show', $patient->id);
            });
        } catch (\Exception $e) {
            alert('Gagal', 'Terjadi kesalahan. Coba lagi nanti', 'error');
            // Jika ada yang error, maka kembali ke halaman sebelumnya dengan input sebelumnya
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        //
    }
}
