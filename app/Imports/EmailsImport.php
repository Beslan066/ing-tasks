<?php

namespace App\Imports;

use App\Models\Email;
use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmailsImport implements ToModel, WithHeadingRow
{
    protected $department;

    public function __construct(Department $department)
    {
        $this->department = $department;
    }

    public function model(array $row)
    {
        return new Email([
            'subject' => $row['subject'],
            'body' => $row['body'],
            'from_email' => $row['from_email'],
            'from_name' => $row['from_name'],
            'to_emails' => json_encode(explode(',', $row['to_emails'])),
            'department_id' => $this->department->id,
            'sent_by' => auth()->id(),
            'sent_at' => now(),
            'received_at' => now(),
        ]);
    }
}
