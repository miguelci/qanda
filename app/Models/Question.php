<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property      string $question
 * @property      string $answer
 * @property-read bool $has_correct_answer
 */
final class Question extends Model
{
    /**
     * {@inheritdoc} 
     */
    public $timestamps = false;

    /**
     * {@inheritdoc} 
     */
    protected $fillable = ['question', 'answer'];

    /**
     * @inheritdoc 
     */
    protected $appends = ['has_correct_answer'];

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function getHasCorrectAnswerAttribute(): bool
    {
        return $this->attributes['has_correct_answer'] = $this->scores()
            ->where('answer', '=', $this->attributes['answer'])
            ->count() > 0;
    }
}
