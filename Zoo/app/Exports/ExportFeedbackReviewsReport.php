<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportFeedbackReviewsReport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $reviews;
    protected $starsFilter;
    protected $fromdate;
    protected $todate;
    protected $currentDate;

    public function __construct($reviews,$starsFilter,$fromdate,$todate,$currentDate) {
        $this->reviews = $reviews;
        $this->starsFilter = $starsFilter;
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
        foreach ($this->reviews as $review) {
            $data[] = [
                'Id' => $review->id,
                'Visitor Name' => $review->visitor_name,
                'Feedback' => $review->description,
                'Stars' => $review->stars,
                'Submitted At' => $review->created_at->format('Y-m-d H:i:s'),
            ];
        }
        return new Collection($data);
    }

    public function headings(): array
    {
        return [
        ['Feedback and Reviews Report'],
        ['Exported on: '.$this->currentDate],
        [
            'Id',
            'Visitor Name',
            'Feedback',
            'Stars',
            'Submitted At',
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
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFC107']],
            ],
        ];
    }
}
