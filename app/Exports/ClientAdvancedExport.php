<?php

namespace App\Exports;

use App\Models\Client;
use App\Support\Layouts\AdvancedLayout;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ClientAdvancedExport implements FromCollection, WithHeadings, Responsable
{
    public string $fileName;
    public string $writerType;
    protected string $extension;

    public function __construct(
        protected Client $client,
        string $extension = 'xlsx'
    ) {
        $this->extension = strtolower($extension);
        $this->fileName   = 'client_' . $client->id . '_advanced.' . $extension;
        $this->writerType = $extension === 'csv'
            ? \Maatwebsite\Excel\Excel::CSV
            : \Maatwebsite\Excel\Excel::XLSX;
    }

    public function headings(): array
    {
        return AdvancedLayout::headings();
    }

    public function collection(): Collection
    {
        $assignments = $this->client->assignments()
            ->with(['device.model', 'sim', 'vehicle'])
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $rows = AdvancedLayout::map($assignments);

        // Return rows strictly in ORDER
        return collect($rows)->map(function (array $r) {
            return collect(\App\Support\Layouts\AdvancedLayout::ORDER)
                ->map(fn ($k) => $r[$k] ?? '')
                ->values();
        });
    }

    /**
     * Implement Responsable interface
     */
    public function toResponse($request)
    {
        if ($this->extension === 'pdf') {
            $rows = collect($this->collection())->values();

            $pdf = Pdf::loadView('exports.client_advanced', [
                    'client'   => $this->client,
                    'headings' => $this->headings(),
                    'rows'     => $rows,
                ])
                ->setPaper('a3', 'landscape')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled'      => true,      // allow loading font files
                    'defaultFont'          => 'Amiri',
                ]);

            return $pdf->download($this->fileName);
        }
        return Excel::download($this, $this->fileName, $this->writerType);
    }
}
