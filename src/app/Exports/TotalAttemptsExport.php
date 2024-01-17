<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class TotalAttemptsExport implements FromCollection, WithHeadings
{
    protected $totalAttempts;
    protected $questions;

    public function __construct($totalAttempts, $questions)
    {
        $this->totalAttempts = $totalAttempts;
        $this->questions = $questions;
    }

    public function collection()
    {
        $data = [];
        foreach ($this->totalAttempts as $key => $attempt) {
            $row = [
                'question' => $this->questions[$key],
                'total_attempts' => $attempt['total_attempts'],
                'correct_attempts' => $attempt['correct_attempts'],
                'incorrect_attempts' => $attempt['incorrect_attempts'],
            ];
            $data[] = $row;
        }
        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Questions',
            'Total Attempts',
            'Correct Attempts',
            'Incorrect Attempts',
        ];
    }
}
