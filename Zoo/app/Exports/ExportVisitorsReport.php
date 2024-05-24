<?php

namespace App\Exports;

use App\Models\Visitors;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportVisitorsReport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $visitors;
    protected $fromdate;
    protected $todate;
    protected $selectedCountry;
    protected $currentDate;
    protected $type;

    public function __construct($visitors, $fromdate, $todate, $selectedCountry, $currentDate, $type)
    {
        $this->visitors = $visitors;
        $this->fromdate = $fromdate;
        $this->todate = $todate;
        $this->selectedCountry = $selectedCountry;
        $this->currentDate = $currentDate;
        $this->type = $type;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = [];
        foreach ($this->visitors as $visitor) {
            $data[] = [
                'Id' => $visitor->id,
                'First Name' => $visitor->firstName,
                'Last Name' => $visitor->lastName,
                'Phone' => $visitor->contactNo,
                'Email' => $visitor->email,
                'Country' => $visitor->country,
                'Last Purchase' => $visitor->latestBookingDate(),
                'Visit Date' => $visitor->visitDate(),
                'Status' => $visitor->visitorstatus(),
            ];
        }
        return new Collection($data);
    }

    public function headings(): array
    {
        $sentence = 'Showing ';
        if ($this->type != 'search') {
            if ($this->selectedCountry && $this->fromdate && $this->todate) {
                if (is_array($this->selectedCountry)) {
                    $sentence .= 'visitors who visited from ' . $this->fromdate . ' to ' . $this->todate;
                } elseif ($this->selectedCountry != 'all') {
                    $sentence .= 'visitors who visited from ' . ucfirst($this->selectedCountry) . ' who visited from ' . $this->fromdate . ' to ' . $this->todate;
                } elseif ($this->selectedCountry == 'all') {
                    $sentence .= 'visitors who visited from ' . $this->fromdate . ' to ' . $this->todate;
                }
            } elseif (!is_array($this->selectedCountry)) {
                if ($this->selectedCountry != 'all') {
                    $sentence .= 'visitors who visited from ' . ucfirst($this->selectedCountry);
                } else {
                    $sentence .= 'all visitors';
                }
            }
        }else {
            $sentence = null;
        }
        return [
            ['Visitors Report'],
            ['Exported on: ' . $this->currentDate],
            [$sentence],
            [
                'Id',
                'First Name',
                'Last Name',
                'Contact',
                'Email',
                'Country',
                'Last Purchased',
                'Visit Date',
                'Status'
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
