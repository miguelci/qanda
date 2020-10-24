<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property      int $id
 * @property-read int $question_id
 * @property      string $answer
 */
final class Score extends Model
{
    /**
     * {@inheritdoc} 
     */
    public $timestamps = false;

    /**
     * {@inheritdoc} 
     */
    protected $fillable = ['question_id', 'answer'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
