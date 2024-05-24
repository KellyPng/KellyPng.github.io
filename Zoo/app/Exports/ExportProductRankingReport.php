<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportProductRankingReport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $currentPeriodData;
    protected $filter;
    protected $startDate;
    protected $endDate;
    protected $currentDate;

    public function __construct($currentPeriodData, $filter, $startDate, $endDate, $currentDate)
    {
        $this->currentPeriodData = $currentPeriodData;
        $this->filter = $filter;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->currentDate = $currentDate;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];
        foreach ($this->currentPeriodData as $product) {
            $data[] = [
                'Ranking' => $product['rank'],
                'Ticket' => $product['ticketTypeName'],
                'Quantity Sold' => $product['totalQuantitySold'],
                'Trend' => $this->getTrend($product['percentageChange'],$product['changeDirection']),
            ];
        }
        return new Collection($data);
    }

    public function getTrend($percentageChange, $changeDirection){
        if ($percentageChange !== null) {
            if ($changeDirection==='increase') {
                return $percentageChange.'% Increase';
            }elseif ($changeDirection==='decrease') {
                return $percentageChange.'% Decrease';
            }elseif ($changeDirection==='stable') {
                return $percentageChange.'% Stable';
            }
        }else {
            return 'No Previous Data';
        }
    }

    public function headings():array
    {
        $sentence = 'Showing ';
        if ($this->filter === 'last12months') {
            $sentence .= 'product ranking for the past 12 months';
        } elseif($this->filter === 'last6months'){
            $sentence .= 'product ranking for the past 6 months';
        }elseif($this->filter === 'last3months'){
            $sentence .= 'product ranking for the past 3 months';
        }elseif($this->filter === 'lastmonth'){
            $sentence .= 'last months product ranking';
        }elseif($this->filter === 'thismonth'){
            $sentence .= 'this months product ranking';
        }elseif ($this->filter === 'dateRange') {
            $sentence .= 'filtered from '. $this->startDate. ' to '. $this->endDate;
        }
        return [
            ['Product Ranking Report'],
            ['Exported on: '. $this->currentDate],
            [$sentence],
            [
                'Ranking',
                'Ticket',
                'Quantity Sold',
                'Trend'
            ],
        ];
    }

    public function styles(Worksheet $sheet){
        return [
            1=>[
                'font' => ['bold' => true],
            ],
            2=>[
                'font' => ['bold' => true],
            ],
            3=>[
                'font' => ['bold' => true],
            ],
            4=>[
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC107']],
            ],
        ];
    }
}
