<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportRefundsReport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $refunds;
    protected $fromdate;
    protected $todate;
    protected $currentDate;
    protected $requestType;
    protected $totalAmount;

    public function __construct($refunds, $fromdate, $todate, $currentDate, $requestType, $totalAmount)
    {
        $this->refunds = $refunds;
        $this->fromdate = $fromdate;
        $this->todate = $todate;
        $this->currentDate = $currentDate;
        $this->requestType = $requestType;
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = [];
        foreach ($this->refunds as $refund) {
            $data[] = [
                'Id' => $refund->id,
                'First Name' => $refund->firstName,
                'Last Name' => $refund->lastName,
                'Booking ID' => $refund->bookingID,
                'Reason' => $refund->reasons,
                'Request Date' => $refund->requestDate,
                'Status' => $refund->status,
                'Action Date' => $refund->approveDate,
                'Amount' => $refund->priceRefund,
            ];
        }
        if ($this->requestType === 'approved' || $this->requestType === 'disapproved') {
            $data[] = ['', '', '', '', '', '','','Total:', '$ '.$this->totalAmount];
        }
        return new Collection($data);
    }

    public function headings(): array
    {
        return [
            ['Refunds Report'],
            ['Exported on: ' . $this->currentDate],
            [
                'Id',
                'First Name',
                'Last Name',
                'Booking ID',
                'Reason',
                'Request Date',
                'Status',
                'Action Date',
                'Amount',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $styles =  [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
            3 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC107']],
            ],

        ];
        $rowNumber = $rowNumber = count($this->refunds) + 4;

        if ($this->requestType === 'approved' || $this->requestType === 'disapproved') {
            $styles[$rowNumber] = [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC107']],
            ];
        }
        return $styles;
    }
}
