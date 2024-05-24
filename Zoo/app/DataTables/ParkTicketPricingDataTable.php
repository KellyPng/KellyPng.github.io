<?php

namespace App\DataTables;

use App\Models\ParkTicketPricing;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ParkTicketPricingDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'parkticketpricing.action')
            ->addColumn('parkName', function (ParkTicketPricing $pricing) {
                return $pricing->singleParkTicket->park->parkName;
            })
            ->addColumn('demoCategoryName', function (ParkTicketPricing $pricing) {
                return $pricing->category->demoCategoryName;
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ParkTicketPricing $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('parkticketpricing-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy(1)
                    ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            'parkName',
            'demoCategoryName',
            'price'
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ParkTicketPricing_' . date('YmdHis');
    }
}
