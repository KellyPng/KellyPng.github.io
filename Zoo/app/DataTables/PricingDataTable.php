<?php

namespace App\DataTables;

use App\Models\Pricing;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PricingDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'pricing.action')
            ->addColumn('ticketTypeName', function (Pricing $pricing) {
                return $pricing->ticketType->ticketTypeName;
            })
            ->addColumn('demoCategoryName', function (Pricing $pricing) {
                return $pricing->category->demoCategoryName;
            })
            ->addColumn('is_local', function ($pricing) {
                return $pricing->is_local ? 'true' : 'false';
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Pricing $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('pricing-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->responsive(true)
                    //->dom('Bfrtip')
                    ->orderBy(0,'asc')
                    ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            'ticketTypeName',
            'demoCategoryName',
            'is_local',
            'price'
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Pricing_' . date('YmdHis');
    }
}
