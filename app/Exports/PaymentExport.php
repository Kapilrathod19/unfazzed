<?php
namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use App\Models\Setting;

class PaymentExport implements FromQuery, WithHeadings, WithMapping
{
    protected $columns;
    protected $query;

    public function __construct($columns = [], $query = null)
    {
        $this->columns = $columns;
        $this->query = $query ?? Payment::query();
    }

    public function query()
    {
        return $this->query->with(['booking.service', 'customer']);
    }

    public function map($payment): array
    {
        $data = [];

        $sitesetup = Setting::where('type','site-setup')->where('key', 'site-setup')->first();
        $datetime_setting = $sitesetup ? json_decode($sitesetup->value) : null;
        $date_format = $datetime_setting ? "$datetime_setting->date_format $datetime_setting->time_format" : "Y-m-d H:i:s";

        $service_name = '-';
        if (isset($payment->booking)) {
            if(!empty($payment->booking->bookingPackage)){
                $service_name = optional(optional($payment->booking)->bookingPackage)->name." (".__('messages.service_package').")";
            } else {
                $service_name = optional(optional($payment->booking)->service)->name." (".__('messages.service').")";
            }
        }

        $columnMap = [
            'colID' => fn() => $payment->id,
            'colService' => fn() => $service_name,
            'colUser' => fn() => optional($payment->customer)->display_name ?? '-',
            'colPaymentType' => fn() => ucfirst($payment->payment_type),
            'colStatus' => fn() => str_replace('_', ' ', ucfirst($payment->payment_status)),
            'colDateTime' => fn() => $payment->datetime ? date($date_format, strtotime($payment->datetime)) : '-',
            'colTotalAmount' => fn() => getPriceFormat($payment->total_amount),
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
            'colID' => 'ID',
            'colService' => 'Service',
            'colUser' => 'User',
            'colPaymentType' => 'Payment Type',
            'colStatus' => 'Status',
            'colDateTime' => 'Datetime',
            'colTotalAmount' => 'Total Amount',
        ];

        return array_filter(
            array_map(fn($column) => $headingsMap[$column] ?? null, $this->columns)
        );
    }
}
