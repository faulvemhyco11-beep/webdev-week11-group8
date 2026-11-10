<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends Model
{
    protected $fillable = ['venue_id', 'title', 'description', 'starts_at', 'details'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'details' => 'array'];
    }

    /**
     * One-to-many (inverse): an event happens at one venue.
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Many-to-many: an event has many participants.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class, 'event_participant')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function waitlistedParticipants(): BelongsToMany
    {
        return $this->participants()->wherePivot('status', 'waitlisted');
    }

    /**
     * Polymorphic: an event can have many notes.
     */
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    /**
     * Scope: events that start from now on.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>=', now());
    }

    /**
     * Scope with a parameter: events at one venue.
     */
    public function scopeAtVenue(Builder $query, int $venueId): Builder
    {
        return $query->where('venue_id', $venueId);
    }

    /**
     * Scope: JSONB filter, arrow form.
     */
    public function scopeWithTheme(Builder $query, string $theme): Builder
    {
        return $query->where('details->theme', $theme);
    }

    /**
     * Scope: JSONB filter, containment form.
     */
    public function scopeWithThemeContains(Builder $query, string $theme): Builder
    {
        return $query->whereJsonContains('details', ['theme' => $theme]);
    }

    /**
     * Scope: full-text search on the description.
     */
    public function scopeSearchDescription(Builder $query, string $term): Builder
    {
        return $query->whereFullText('description', $term);
    }
}