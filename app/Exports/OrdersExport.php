<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths
{
    public function collection()
    {
        return Order::with(['client', 'items.product'])
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Client Name',
            'Date',
            'Total Amount (Rp)',
            'Status',
            'Payment Status',
            'Items Count'
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->client->full_name,
            $order->created_at->format('d M Y H:i'),
            $order->total_amount, // Store as number for Excel formatting
            ucfirst($order->status),
            ucfirst($order->payment_status),
            $order->items->count()
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Set title and subtitle
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'ORDERS REPORT');
        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'Generated on: ' . now()->format('d M Y H:i:s'));
        
        $sheet->getStyle('A1:A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Data styles
        $sheet->getStyle('A3:G' . ($sheet->getHighestRow()))
            ->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Number formatting for amount column
        $sheet->getStyle('D3:D' . $sheet->getHighestRow())
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // Auto filter
        $sheet->setAutoFilter('A3:G3');

        // Column alignment
        $sheet->getStyle('D3:D' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle('G3:G' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Conditional formatting for status columns
        $this->applyStatusStyles($sheet, 'E', 'status');
        $this->applyStatusStyles($sheet, 'F', 'payment_status');

        // Borders
        $sheet->getStyle('A3:G' . $sheet->getHighestRow())
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
    }

    protected function applyStatusStyles(Worksheet $sheet, string $column, string $type)
    {
        $lastRow = $sheet->getHighestRow();
        
        foreach (range(3, $lastRow) as $row) {
            $cellValue = $sheet->getCell($column . $row)->getValue();
            $cellValue = strtolower($cellValue);
            
            $colorMap = [
                'status' => [
                    'completed' => '28a745',
                    'cancelled' => 'dc3545',
                    'pending' => 'ffc107',
                    'processing' => '17a2b8',
                ],
                'payment_status' => [
                    'paid' => '28a745',
                    'failed' => 'dc3545',
                    'pending' => '6c757d',
                ]
            ];
            
            $color = $colorMap[$type][$cellValue] ?? '6c757d';
            
            $sheet->getStyle($column . $row)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color],
                ],
                'font' => [
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);
        }
    }

    public function title(): string
    {
        return 'Orders Report';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Order Number
            'B' => 25, // Client Name
            'C' => 18, // Date
            'D' => 18, // Amount
            'E' => 15, // Status
            'F' => 15, // Payment Status
            'G' => 12, // Items Count
        ];
    }
}