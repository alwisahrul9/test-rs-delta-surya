<?php
namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<User> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('roles', function ($user) {
                // EBadge warna untuk Admin, Dokter dan Apoteker
                return $user->roles->map(function ($role) {
                    $color = $role->name == 'doctor' ? 'success' : 'info';
                    $color = $role->name == 'admin' ? 'primary' : $color;
                    return "<span class='badge bg-$color'>" . ucfirst($role->name) . "</span>";
                })->implode(' ');
            })
            ->orderColumn('roles', function ($query, $order) {
                $query->orderBy(
                    DB::table('roles')
                        ->join('model_has_roles as mhr', 'roles.uuid', '=', 'mhr.role_id')
                        ->whereColumn('mhr.model_uuid', 'users.id') // Mengacu langsung ke ID tabel users
                        ->where('mhr.model_type', User::class)
                        ->select('roles.name')
                        ->limit(1),
                    $order
                );
            })
            ->addColumn('action', function (User $user) {
                return view('admin.component.datatable.action.user', compact('user'))->render();
            })
            ->escapeColumns([]) // Mengizinkan tag HTML untuk Badge
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<User>
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->whereNot('id', Auth::id())
            ->with('roles');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-table')
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
            Column::make('DT_RowIndex')->title('No')
                ->searchable(false)
                ->orderable(false)
                ->width(60)
                ->addClass('text-center'),
            Column::make('name'),
            Column::make('email'),
            Column::make('roles')->title('Role'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'User_' . date('YmdHis');
    }
}
