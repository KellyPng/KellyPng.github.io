<?php

namespace App\Exports;

use App\Models\Bookings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportBookingsReport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $bookings;
    protected $totalQuantity;
    protected $totalPrice;
    protected $demographicQuantities;
    protected $currentDate;
    protected $dateFilter;
    protected $fromdate;
    protected $todate;
    protected $filter;

    public function __construct($bookings, $totalQuantity, $totalPrice, $demographicQuantities, $currentDate, $dateFilter,$filter, $fromdate, $todate)
    {
        $this->bookings = $bookings;
        $this->totalQuantity = $totalQuantity;
        $this->totalPrice = $totalPrice;
        $this->demographicQuantities = $demographicQuantities;
        $this->currentDate = $currentDate;
        $this->dateFilter = $dateFilter;
        $this->filter = $filter;
        $this->fromdate = $fromdate;
        $this->todate = $todate;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = [];
        foreach ($this->bookings as $booking){
            $data[] = [
                'Id' => $booking->bookingID,
                'Visitor Name' => $booking->visitor->firstName . ' ' . $booking->visitor->lastName,
                'Ticket Type' => $this->getTicketType($booking),
                'Visit Date' => $booking->bookingDate,
                'Booking Date' => $booking->created_at->format('Y-m-d H:i:s'),
                'Ticket Status' => $booking->bookingStatus == 0 ? 'Valid' : 'Used',
                'Quantity' => $this->getDemoQuantitiesString($booking),
                'Total Price' => $booking->totalPrice
            ];
        }
        $data[] = [
            '', '', '', '', '', 'Total', $this->totalQuantity, $this->totalPrice
        ];
        return new Collection($data);
    }

    public function headings(): array
    {
        $dateRange = 'Showing ';

        if ($this->dateFilter === 'visitdate') {
            if ($this->filter==='last12months') {
                $dateRange .= 'bookings for the last 12 months based on visit date.';
            }elseif ($this->filter==='last6months') {
                $dateRange .= 'bookings for the last 6 months based on visit date.';
            }elseif ($this->filter==='last3months') {
                $dateRange .= 'bookings for the last 3 months based on visit date.';
            }elseif ($this->filter==='lastmonth') {
                $dateRange .= 'last month bookings based on visit date.';
            }elseif ($this->filter==='thismonth') {
                $dateRange .= 'this month bookings based on visit date.';
            }elseif ($this->filter==='dateRange') {
                $dateRange .= 'bookings from ' . $this->fromdate . ' to ' . $this->todate . ' based on visit date.';
            }
        } elseif($this->dateFilter === 'bookingdate'){
            if ($this->filter==='last12months') {
                $dateRange .= 'bookings for the last 12 months based on booking date.';
            }elseif ($this->filter==='last6months') {
                $dateRange .= 'bookings for the last 6 months based on booking date.';
            }elseif ($this->filter==='last3months') {
                $dateRange .= 'bookings for the last 3 months based on booking date.';
            }elseif ($this->filter==='lastmonth') {
                $dateRange .= 'last month bookings based on booking date.';
            }elseif ($this->filter==='thismonth') {
                $dateRange .= 'this month bookings based on booking date.';
            }elseif ($this->filter==='dateRange') {
                $dateRange .= 'bookings from ' . $this->fromdate . ' to ' . $this->todate . ' based on booking date.';
            }
        }else {
            $dateRange .= 'all bookings';
        }

        return [
            ['Bookings Report'],
            [
                'Exported on: ' . $this->currentDate
            ],
            [$dateRange],
            [
                'Id',
                'Visitor Name',
                'Ticket Type',
                'Visit Date',
                'Booking Date',
                'Ticket Status',
                'Quantity',
                'Total Price'
            ],
        ];
    }

    public function footer(): array
    {
        return [
            ['', '', '', '', '', 'Total', $this->totalQuantity, '$ '.$this->totalPrice]
        ];
    }

    

    public function getTicketType($booking):string{
        if ($booking->ticketType->ticketTypeName == 'Single Park'){
            return $booking->ticketType->ticketTypeName . ' : ' . $booking->bookParks->park->parkName;
        }else{
            return $booking->ticketType->ticketTypeName;
        }
                             
    }

    public function getDemoQuantitiesString($booking):string{
        $demographicQuantitiesString = '';
        if (isset($this->demographicQuantities[$booking->id])){
            foreach ($this->demographicQuantities[$booking->id] as $categoryName => $quantity)
                $demographicQuantitiesString .= $categoryName. ' : ' .  $quantity . ' '."\n";
                                   
        }
        return $demographicQuantitiesString;
    }

    public function styles(Worksheet $sheet){
        return[
            1 =>[
                'font' => ['bold' => true],
            ],
            2 =>[
                'font' => ['bold' => true],
            ],
            3 =>[
                'font' => ['bold' => true],
            ],
            4 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC107']],
            ],

            count($this->bookings) + 5 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC107']],
            ],
        ];
    }
}
