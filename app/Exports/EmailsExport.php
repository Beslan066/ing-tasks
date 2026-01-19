<?php

namespace App\Exports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmailsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $department;

    public function __construct(Department $department)
    {
        $this->department = $department;
    }

    public function collection()
    {
        return $this->department->emails()
            ->with(['sender', 'tags'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Тема',
            'От кого',
            'Кому',
            'Дата отправки',
            'Статус',
            'Теги',
            'Размер вложений',
        ];
    }

    public function map($email): array
    {
        return [
            $email->id,
            $email->subject,
            $email->from_name,
            implode(', ', $email->to_emails),
            $email->sent_at?->format('d.m.Y H:i'),
            $email->is_draft ? 'Черновик' : 'Отправлено',
            $email->tags->pluck('name')->implode(', '),
            formatBytes($email->getAttachmentsSize()),
        ];
    }
}
