<?php

namespace App\Exports;

use App\Models\Attendance;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected string $month;
    protected ?string $employeeId;

    public function __construct(string $month, ?string $employeeId = null)
    {
        $this->month = $month;
        $this->employeeId = $employeeId;
    }

    public function collection()
    {
        $query = Attendance::with('user')
            ->whereYear('date', Carbon::parse($this->month)->year)
            ->whereMonth('date', Carbon::parse($this->month)->month)
            ->orderBy('date', 'asc');

        if ($this->employeeId) {
            $query->where('user_id', $this->employeeId);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Karyawan',
            'Tanggal',
            'Check In',
            'Check Out',
            'Status',
        ];
    }

    public function map($attendance): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $attendance->user->name ?? '-',
            Carbon::parse($attendance->date)->format('d/m/Y'),
            $attendance->check_in ?? '-',
            $attendance->check_out ?? '-',
            ucfirst($attendance->status),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
