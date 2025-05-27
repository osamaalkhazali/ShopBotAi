<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    // Define status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';
    const STATUS_FLAGGED = 'flagged';

    protected $fillable = [
        'user_id',
        'name',
        'status',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    // Set default values for attributes
    protected $attributes = [
        'status' => self::STATUS_ACTIVE,
        'tags' => '[]',
    ];

    /**
     * Get the user that owns this chat session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the messages for this chat session.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'session_id')->orderBy('order');
    }

    /**
     * Get message count for this session
     */
    public function messageCount()
    {
        return $this->messages()->count();
    }

    /**
     * Check if session is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if session is closed
     */
    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    /**
     * Check if session is flagged
     */
    public function isFlagged(): bool
    {
        return $this->status === self::STATUS_FLAGGED;
    }

    /**
     * Close this session
     */
    public function close()
    {
        $this->update(['status' => self::STATUS_CLOSED]);
    }

    /**
     * Reopen this session
     */
    public function reopen()
    {
        $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Flag this session
     */
    public function flag()
    {
        $this->update(['status' => self::STATUS_FLAGGED]);
    }

    /**
     * Add a tag to this session
     */
    public function addTag(string $tag)
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    /**
     * Remove a tag from this session
     */
    public function removeTag(string $tag)
    {
        $tags = $this->tags ?? [];
        $this->update(['tags' => array_values(array_filter($tags, fn($t) => $t !== $tag))]);
    }
}
