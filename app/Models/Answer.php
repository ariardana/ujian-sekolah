<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_participant_id',
        'question_id',
        'selected_option_id',
        'answer_text',
        'is_flagged',
        'is_correct',
        'score',
        'teacher_note',
    ];

    protected $casts = [
        'is_flagged' => 'boolean',
        'is_correct' => 'boolean',
        'score' => 'decimal:2',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(ExamParticipant::class, 'exam_participant_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }
}
