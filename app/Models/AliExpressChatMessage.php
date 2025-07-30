<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AliExpressChatMessage extends Model
{
    // Specify the table name to avoid snake_case conversion
    protected $table = 'aliexpress_chat_messages';

    protected $fillable = [
        'session_id',
        'sender',
        'content',
        'order',
        'is_flagged',
    ];

    /**
     * Get the session that owns this message.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(AliExpressChatSession::class, 'session_id');
    }
}
