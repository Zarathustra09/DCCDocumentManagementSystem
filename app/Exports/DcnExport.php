<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\DocumentRegistrationEntry;
use App\Models\Export;
use App\Models\SubCategory;
use App\Models\User;
use App\Notifications\ExportReadyNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;

class DcnExport implements WithMultipleSheets, ShouldQueue
{
    protected $subcategoryName;
    protected int $exportId;
    protected ?int $subcategoryId;

    public function __construct(int $exportId, $subcategoryName = null, ?int $subcategoryId = null)
    {
        $this->exportId = $exportId;
        $this->subcategoryId = $subcategoryId;
        $this->subcategoryName = $subcategoryName;

        if ($this->subcategoryId && !$this->subcategoryName) {
            $this->subcategoryName = SubCategory::whereKey($this->subcategoryId)->value('name');
        }
    }

    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new DcnCustomerSheet(null, $this->cleanTitle('ALL'), $this->subcategoryName, $this->subcategoryId);

        $hasNoCustomer = DocumentRegistrationEntry::whereNull('customer_id')
            ->when($this->subcategoryId, fn($q) => $q->where('category_id', $this->subcategoryId))
            ->exists();

        if ($hasNoCustomer) {
            $sheets[] = new DcnCustomerSheet('__no_customer__', $this->cleanTitle('NO-CUSTOMER'), $this->subcategoryName, $this->subcategoryId);
        }

        $customers = Customer::whereHas('documentRegistrationEntries', function ($q) {
                if ($this->subcategoryId) {
                    $q->where('category_id', $this->subcategoryId);
                }
            })
            ->orderBy('code')
            ->get();

        foreach ($customers as $customer) {
            $title = $customer->code ?: ('CUSTOMER-' . $customer->id);
            $sheets[] = new DcnCustomerSheet($customer->id, $this->cleanTitle($title), $this->subcategoryName, $this->subcategoryId);
        }

        // Mark the last sheet with export ID for notification
        if (!empty($sheets)) {
            $lastSheet = $sheets[count($sheets) - 1];
            $lastSheet->setExportId($this->exportId);
        }

        Log::info('DcnExport sheets created', [
            'export_id' => $this->exportId,
            'total_sheets' => count($sheets),
        ]);

        return $sheets;
    }

    protected function cleanTitle(string $title): string
    {
        $title = trim($title) ?: 'Sheet';
        $title = preg_replace('/[\\\\:\\/?\\*\\[\\]]/', '-', $title);
        return mb_substr($title, 0, 31);
    }
}

class DcnCustomerSheet implements FromQuery, WithHeadings, WithMapping, WithTitle, WithChunkReading, WithEvents
{
    use RegistersEventListeners;

    protected $customerId;
    protected $title;
    protected $subcategoryName;
    protected ?int $subcategoryId;
    protected ?int $exportId = null;

    public function __construct($customerId, string $title, $subcategoryName = null, ?int $subcategoryId = null)
    {
        $this->customerId = $customerId;
        $this->title = $title;
        $this->subcategoryName = $subcategoryName;
        $this->subcategoryId = $subcategoryId;
    }

    public function setExportId(int $exportId): void
    {
        $this->exportId = $exportId;
    }

    public static function afterSheet(AfterSheet $event): void
    {
        $sheet = $event->getConcernable();

        if ($sheet instanceof self && $sheet->exportId) {
            Log::info('Last sheet processed, completing export', [
                'export_id' => $sheet->exportId,
                'sheet_title' => $sheet->title,
            ]);

            $export = Export::find($sheet->exportId);

            if ($export) {
                $export->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                Log::info('Export completed, sending notification', [
                    'export_id' => $export->id,
                    'control_no' => $export->control_no,
                    'employee_no' => $export->employee_no,
                ]);

                $user = User::where('employee_no', $export->employee_no)->first();

                if ($user) {
                    $user->notify(new ExportReadyNotification($export));

                    Log::info('Export notification sent', [
                        'export_id' => $export->id,
                        'control_no' => $export->control_no,
                        'user_id' => $user->id,
                        'employee_no' => $user->employee_no,
                    ]);
                } else {
                    Log::warning('User not found for export notification', [
                        'export_id' => $export->id,
                        'control_no' => $export->control_no,
                        'employee_no' => $export->employee_no,
                    ]);
                }
            }
        }
    }

    public function query()
    {
        $query = DocumentRegistrationEntry::query()
            ->with(['customer', 'category', 'submittedBy', 'status'])
            ->orderBy('submitted_at', 'desc')
            ->orderBy('created_at', 'desc');

        if ($this->subcategoryId) {
            $query->where('category_id', $this->subcategoryId);
        }

        if ($this->customerId === '__no_customer__') {
            $query->whereNull('customer_id');
        } elseif (!is_null($this->customerId)) {
            $query->where('customer_id', $this->customerId);
        }

        return $query;
    }

    public function headings(): array
    {
        if ($this->subcategoryName && strcasecmp($this->subcategoryName, 'Document Special Instruction') === 0) {
            return [
                'DCN',
                'Originator',
                'Dept.',
                'Reg-Date',
                'EXPIRATION DATE',
                'Title of Doc',
                'Remarks',
            ];
        }

        if ($this->subcategoryName && strcasecmp($this->subcategoryName, 'SPI In-House Specification') === 0) {
            return [
                'DCN No.',
                'Originator',
                'Dept.',
                'Date Registered',
                'Document No.',
                'Rev No.',
                'Title',
                'Remarks',
            ];
        }

        return [
            'DCN No.',
            'Originator',
            'Dept.',
            'Reg-Date',
            'EFFECTIVE DATE',
            'Document No. / Part No.',
            'Rev',
            'Device Name',
            'Title',
            'Customer',
            'Remarks',
        ];
    }

    public function map($entry): array
    {
        if ($this->subcategoryName && strcasecmp($this->subcategoryName, 'Document Special Instruction') === 0) {
            return [
                $entry->dcn_no,
                $entry->originator_name,
                $entry->dept ?? null,
                $entry->submitted_at,
                $entry->expiration_date ?? null,
                $entry->document_title,
                $entry->remarks ?? null,
            ];
        }

        if ($this->subcategoryName && strcasecmp($this->subcategoryName, 'SPI In-House Specification') === 0) {
            return [
                $entry->dcn_no,
                $entry->originator_name,
                $entry->dept ?? null,
                $entry->submitted_at,
                $entry->document_no,
                $entry->revision_no,
                $entry->document_title,
                $entry->remarks ?? null,
            ];
        }

        return [
            $entry->dcn_no,
            $entry->originator_name,
            $entry->dept ?? null,
            $entry->submitted_at,
            $entry->implemented_at,
            $entry->document_no,
            $entry->revision_no,
            $entry->device_name,
            $entry->document_title,
            $entry->customer->code ?? ($entry->customer->name ?? null),
            $entry->remarks ?? null,
        ];
    }

    public function title(): string
    {
        return $this->title;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}

