<?php

namespace App\Exports;

use App\Models\DeviceMonitoring;
use App\Models\ClientDevice;
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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DeviceMonitoringExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths
{
    protected $deviceId;
    protected $deviceName;
    protected $deviceStatus;

    public function __construct($deviceId)
    {
        $device = ClientDevice::find($deviceId);
        $this->deviceId = $deviceId;
        $this->deviceName = $device->device_name ?? 'Unknown Device';
        $this->deviceStatus = $device->status ?? 'unknown';
    }

    public function collection()
    {
        return DeviceMonitoring::where('device_id', $this->deviceId)
            ->latest('recorded_at')
            ->take(2000)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Timestamp',
            'Voltage (V)',
            'Current (A)',
            'Power (W)',
            'Energy (kWh)',
            'Frequency (Hz)',
            'Power Factor',
            'Device Status'
        ];
    }

    public function map($data): array
    {
        return [
            $data->recorded_at->format('Y-m-d H:i:s'),
            $data->voltage,
            $data->current,
            $data->power,
            $data->energy,
            $data->frequency,
            $data->power_factor,
            ucfirst($this->deviceStatus)
        ];
    }
public function styles(Worksheet $sheet)
{
    // First write the headings at row 3
    $sheet->fromArray($this->headings(), null, 'A3');
    
    // Set title (row 1)
    $sheet->mergeCells('A1:H1');
    $sheet->setCellValue('A1', 'MONITORING DATA - ' . strtoupper($this->deviceName));
    $sheet->getStyle('A1')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 14,
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ],
    ]);

    // Set subtitle (row 2)
    $sheet->mergeCells('A2:H2');
    $sheet->setCellValue('A2', 'D export pada: ' . now()->format('d M Y H:i:s'));
    $sheet->getStyle('A2')->applyFromArray([
        'font' => [
            'italic' => true,
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ],
    ]);

    // Style the headers (row 3)
    $sheet->getStyle('A3:H3')->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['rgb' => Color::COLOR_WHITE],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '2c3e50'],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ]);

    // Style data rows
    $sheet->getStyle('A4:H' . $sheet->getHighestRow())
        ->getAlignment()
        ->setVertical(Alignment::VERTICAL_CENTER);

    // Number formatting
    $numberFormats = [
        'B' => '#,##0.000',    // Voltage
        'C' => '#,##0.000',   // Current
        'D' => '#,##0.000',    // Power
        'E' => '#,##0.000',   // Energy
        'F' => '#,##0.000',    // Frequency
        'G' => '0.00',        // Power Factor
    ];

    foreach ($numberFormats as $column => $format) {
        $sheet->getStyle($column . '4:' . $column . $sheet->getHighestRow())
            ->getNumberFormat()
            ->setFormatCode($format);
    }

    // Column alignment
    $sheet->getStyle('B4:H' . $sheet->getHighestRow())
        ->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

    // Auto filter (for headers)
    $sheet->setAutoFilter('A3:H3');

    // Conditional formatting for device status
    $statusColumn = 'H';
    $lastRow = $sheet->getHighestRow();
    
    for ($row = 4; $row <= $lastRow; $row++) {
        $status = strtolower($sheet->getCell($statusColumn . $row)->getValue());
        $color = $status === 'active' ? '27ae60' : 'e74c3c';
        
        $sheet->getStyle($statusColumn . $row)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $color],
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF'],
                'bold' => true
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }

    // Borders
    $sheet->getStyle('A3:H' . $lastRow)
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

    // Freeze headers
    $sheet->freezePane('A4');
}

    public function title(): string
    {
        return 'Monitoring Data';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Timestamp
            'B' => 12, // Voltage
            'C' => 12, // Current
            'D' => 12, // Power
            'E' => 12, // Energy
            'F' => 12, // Frequency
            'G' => 12, // Power Factor
            'H' => 15  // Device Status
        ];
    }
}