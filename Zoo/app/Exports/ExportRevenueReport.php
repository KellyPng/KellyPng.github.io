<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithEvents;
// use PhpOffice\PhpSpreadsheet\Worksheet\Chart;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;

class ExportRevenueReport implements FromCollection, ShouldAutoSize, WithStyles, WithHeadings
{
    protected $filter;
    protected $getRevenueData;
    protected $dateRange;
    protected $totalRevenue;
    protected $startDate;
    protected $endDate;
    protected $currentDate;

    public function __construct($filter, $dateRange, $getRevenueData,$totalRevenue,$startDate,$endDate,$currentDate)
    {
        $this->filter = $filter;
        $this->dateRange = $dateRange;
        $this->getRevenueData = $getRevenueData;
        $this->currentDate = $currentDate;
        $this->totalRevenue = $totalRevenue;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $data=[];
        foreach ($this->getRevenueData as $revenue) {
            $data[] = [
                'Date' => $revenue->month,
                'Bookings' => $revenue->total_bookings,
                'Sales' => '$ '.$revenue->total_sales,
                'Refunds' => '$ '.$revenue->total_refunds,
                'Total' => '$ '.$revenue->total_sales - $revenue->total_refunds
            ];
        }
        $data[] = [
            '', '', '', 'Total:', '$ '.$this->totalRevenue
        ];
        return new Collection($data);
    }

    public function headings():array
    {
        $sentence = 'Showing ';

        if ($this->filter === 'last12months') {
            $sentence .= 'revenue for the last 12 months';
        } elseif($this->filter === 'last6months'){
            $sentence .= 'revenue for the last 6 months';
        }elseif($this->filter === 'last3months'){
            $sentence .= 'revenue for the last 3 months';
        }elseif($this->filter === 'lastmonths'){
            $sentence .= 'last months revenue';
        }elseif($this->filter === 'thismonths'){
            $sentence .= 'this months revenue';
        }elseif ($this->filter === 'dateRange') {
            $sentence .= 'revenue from '. $this->startDate. ' to '. $this->endDate;
        }
        return [
            ['Revenue Report'],
            ['Exported on: '. $this->currentDate],
            [$sentence],
            [
                'Date',
                'Bookings',
                'Sales',
                'Refunds',
                'Total'
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $styles =  [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
            3 => [
                'font' => ['bold' => true]],
            4=>[
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC107']],
            ],
            count($this->getRevenueData) + 5=>[
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC107']],
            ],

        ];
        
        return $styles;
    }

}
