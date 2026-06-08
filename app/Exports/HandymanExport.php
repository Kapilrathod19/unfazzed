<?php
namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class HandymanExport implements FromQuery, WithHeadings, WithMapping
{
    protected $columns;
    protected $query;
    protected $rowNumber = 0;

    public function __construct($columns = [], $query = null)
    {
        $this->columns = $columns;
        $this->query = $query ?? User::where('user_type', 'handyman');
    }

    public function query()
    {
        return $this->query->with('providers');
    }

    public function map($handyman): array
    {
        $data = [];

        $this->rowNumber++;

        $columnMap = [
            'colID' => fn() => $this->rowNumber,
            'colName' => fn() => $handyman->display_name ?? '-',
            'colEmail' => fn() => $handyman->email ?? '-',
            'colContact' => fn() => $handyman->contact_number ?? '-',
            'colProvider' => fn() => optional($handyman->providers)->display_name ?? '-',
            'colStatus' => fn() => $handyman->status == 1 ? 'Active' : 'Inactive',
            'colJoiningDate' => fn() => $handyman->created_at ? Carbon::parse($handyman->created_at)->format('Y-m-d H:i:s') : '-',
        ];

        foreach ($this->columns as $column) {
            if (isset($columnMap[$column])) {
                $data[] = $columnMap[$column]();
            }
        }

        return $data;
    }

    public function headings(): array
    {
        $headingsMap = [
            'colID' => 'Sr. No',
            'colName' => 'Name',
            'colEmail' => 'Email',
            'colContact' => 'Contact Number',
            'colProvider' => 'Provider',
            'colStatus' => 'Status',
            'colJoiningDate' => 'Joining Date',
        ];

        return array_filter(
            array_map(fn($column) => $headingsMap[$column] ?? null, $this->columns)
        );
    }
}
