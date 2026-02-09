<?php

namespace App\Services\Seo\Schemas;

class EventSchema extends BaseSchema
{
    public function __construct(
        protected string $name,
        protected string $description,
        protected string $url,
        protected string $startDate,
        protected ?string $endDate = null,
        protected ?string $location = null,
        protected ?string $locationAddress = null,
        protected ?string $image = null,
        protected ?string $organizerName = null,
        protected ?string $organizerUrl = null,
        protected ?string $eventStatus = 'EventScheduled', // EventCancelled, EventPostponed, EventRescheduled
        protected ?string $eventAttendanceMode = 'OfflineEventAttendanceMode' // OnlineEventAttendanceMode, MixedEventAttendanceMode
    ) {}

    public function toArray(): array
    {
        $data = [
            '@type' => 'Event',
            '@id' => $this->url . '#event',
            'name' => $this->name,
            'description' => strip_tags($this->description),
            'url' => $this->url,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'image' => $this->image,
            'eventStatus' => 'https://schema.org/' . $this->eventStatus,
            'eventAttendanceMode' => 'https://schema.org/' . $this->eventAttendanceMode,
        ];

        // Location
        if ($this->location) {
            $data['location'] = [
                '@type' => 'Place',
                'name' => $this->location,
                'address' => $this->locationAddress ?? $this->location,
            ];
        }

        // Organizer
        if ($this->organizerName) {
            $data['organizer'] = [
                '@type' => 'Organization',
                'name' => $this->organizerName,
                'url' => $this->organizerUrl ?? config('app.url'),
            ];
        }

        return $this->clean($data);
    }
}
