<?php

namespace App\Imports;

use App\Models\BankQuestion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BankQuestionImport implements ToModel, WithHeadingRow
{
    protected $partId;

    public function __construct($partId)
    {
        $this->partId = $partId;
    }

    public function model(array $row)
    {
        // 1. Ekstrak data dari kolom Excel (Dengan fallback nama header yang fleksibel)
        $questionText = trim(
            $row['pertanyaan_teks_soal'] ?? 
            $row['soal'] ?? 
            $row['pertanyaan'] ?? 
            reset($row) ?? ''
        );

        $optA = trim($row['opsi_a'] ?? $row['pilihan_a'] ?? '');
        $optB = trim($row['opsi_b'] ?? $row['pilihan_b'] ?? '');
        $optC = trim($row['opsi_c'] ?? $row['pilihan_c'] ?? '');
        $optD = trim($row['opsi_d'] ?? $row['pilihan_d'] ?? '');
        
        $rawAnswer = trim(
            $row['jawaban_benar'] ?? 
            $row['kunci_jawaban'] ?? 
            $row['jawaban'] ?? ''
        );

        $explanation = trim(
            $row['pembahasan'] ?? 
            $row['keterangan'] ?? ''
        );

        // Jika teks soal atau kunci jawaban kosong, lewati baris ini
        if (empty($questionText) || empty($rawAnswer)) {
            return null;
        }

        // 🧠 LOGIKA SMART DETECT: Pilihan Ganda vs Essay
        // Jika Opsi A kosong atau berisi tanda strip (-), jadikan Mode Essay
        $isEssay = empty($optA) || $optA === '-';

        if ($isEssay) {
            $type = 'essay';
            $options = [];
            
            // 🧠 LOGIKA BARU MULTI-BLANK & MULTI-ALIAS:
            // 1. Pecah kunci jawaban berdasarkan pemisah antar-blank ('|' atau ';')
            $rawBlanks = array_map('trim', preg_split('/[|;]/', $rawAnswer));
            $parsedAnswers = [];

            foreach ($rawBlanks as $blank) {
                if (!empty($blank)) {
                    // 2. Pecah variasi sinonim dalam 1 blank berdasarkan ('/' atau ',')
                    $aliases = array_map(function($item) {
                        return mb_strtolower(trim($item));
                    }, preg_split('/[\/,]/', $blank));
                    
                    $parsedAnswers[] = array_values(array_filter($aliases));
                }
            }

            // Simpan ke DB dalam format JSON array 2 Dimensi: [["bandung", "bandoeng"], ["jawa barat", "jabar"]]
            $correctAnswer = json_encode($parsedAnswers);
        } else {
            $type = 'multiple_choice';
            $options = [
                'A' => $optA,
                'B' => $optB,
                'C' => $optC,
                'D' => $optD,
            ];
            $correctAnswer = strtoupper($rawAnswer);
        }

        return new BankQuestion([
            'bank_part_id'  => $this->partId,
            'type'          => $type,
            'question_text' => $questionText,
            'options'       => $options,
            'correct_answer'=> $correctAnswer,
            'explanation'   => !empty($explanation) ? $explanation : null,
            'image'         => null,
            'audio'         => null,
        ]);
    }
}