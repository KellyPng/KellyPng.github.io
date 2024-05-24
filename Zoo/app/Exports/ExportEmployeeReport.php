<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportEmployeeReport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $reports;
    protected $filter;
    protected $fromdate;
    protected $todate;
    protected $currentDate;

    public function __construct($reports, $filter, $fromdate, $todate, $currentDate)
    {
        $this->reports = $reports;
        $this->filter = $filter;
        $this->fromdate = $fromdate;
        $this->todate = $todate;
        $this->currentDate = $currentDate;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = [];
        foreach ($this->reports as $report) {
            $data[] = [
                'Id' => $report['id'],
                'Subject' => $report['subject'],
                'Description' => $report['description'],
                'Employee Email' => $report['email'],
                'Submitted At' => $report['created_at'],
            ];
        }
        return new Collection($data);
    }

    public function headings(): array
    {
        $dateRange = 'Showing ';
        if ($this->filter) {
            if ($this->filter === 'dateRange') {
                $dateRange .= 'employee reports submitted from ' . $this->fromdate . ' to ' . $this->todate;
            } elseif ($this->filter === 'thisweek') {
                $dateRange .= 'employee reports submitted this week.';
            } elseif ($this->filter === 'last6months') {
                $dateRange .= 'employee reports submitted the past 6 months.';
            } elseif ($this->filter === 'last3months') {
                $dateRange .= 'employee reports submitted the past 3 months.';
            } elseif ($this->filter === 'lastmonth') {
                $dateRange .= 'employee reports submitted last month.';
            } elseif ($this->filter === 'thismonth') {
                $dateRange .= 'employee reports submitted this month.';
            }
        }else{
            $dateRange = null;
        }

        return [
            ['Employee Reports'],
            [
                'Exported on: ' . $this->currentDate
            ],
            [$dateRange],
            [
                'Id',
                'Subject',
                'Description',
                'Employee Email',
                'Submitted At'
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
            2 => [
                'font' => ['bold' => true],
            ],
            3 => [
                'font' => ['bold' => true],
            ],
            4 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC107']],
            ],
        ];
    }
}
