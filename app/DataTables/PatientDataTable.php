<?php
namespace App\DataTables;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PatientDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Patient> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($patient) {
                return view('global-component.datatable.action.patient', compact('patient'))->render();
            })
            // Tambahkan format untuk tanggal lahir agar lebih user-friendly
            ->editColumn('born_date', function ($patient) {
                return \Carbon\Carbon::parse($patient->born_date)->format('d F Y');
            })
            // Tambahkan format untuk jenis kelamin
            ->editColumn('sex', function ($patient) {
                return $patient->sex == 'm' ? 'Laki-laki' : 'Perempuan';
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Patient>
     */
    public function query(Patient $model): QueryBuilder
    {
        $query = $model->newQuery()->with('examinations');

        // Jika ada input pencarian (misal di kolom search), jangan filter berdasarkan dokter
        // Tapi jika tidak sedang mencari, tampilkan pasien yang pernah ditangani
        if (request()->filled('search.value') || (Auth::user()->hasRole('admin') || Auth::user()->hasRole('pharmacist'))) {
            return $query; // Tampilkan semua pasien yang cocok dengan kata kunci pencarian
        }

        return $query->whereHas('examinations', function ($q) {
            $q->where('user_id', Auth::id());
        });
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('patient-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->dom('Blfrtip')
            ->parameters([
                'responsive' => true,  // Mengaktifkan fitur responsif bawaan DataTables
                'autoWidth'  => false, // Mencegah DataTables menghitung lebar secara kaku
            ])
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload'),
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            // Gunakan nama kolom asli database pada Column::make agar fungsi sorting & searching berjalan
            Column::make('DT_RowIndex')->title('No')
                ->searchable(false)
                ->orderable(false)
                ->width(60)
                ->addClass('text-center'),
            Column::make('name')->title('Nama Pasien'),
            Column::make('born_date')->title('Tanggal Lahir'),
            Column::make('sex')->title('Jenis Kelamin'),
            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Patient_' . date('YmdHis');
    }
}
