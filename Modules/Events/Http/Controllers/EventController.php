<?php

namespace Modules\Events\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Events\Models\Event;
use Modules\Events\Models\EventCategory;
use Illuminate\Support\Str;

class EventController extends Controller
{

    public function index()
    {
        // Helper to format event data
        $formatEvent = function ($event) {
            $daysRemaining = round(now()->diffInDays($event->start_date, false));
            $isFinished = $daysRemaining < 0;

            return [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'description' => Str::limit(strip_tags($event->description), 100),
                'date' => [
                    'day' => $event->start_date->format('d'),
                    'month' => strtoupper($event->start_date->format('M')),
                    'year' => $event->start_date->format('Y'),
                ],
                'time' => $event->start_date->format('h:i A') . ' - ' . ($event->end_date ? $event->end_date->format('h:i A') : 'End'),
                'location' => $event->location,
                'price' => $event->price ?? 'Free',
                'quota' => $event->quota,
                'available_seats' => $event->available_seats,
                'image' => $event->thumbnail ? '/storage/' . $event->thumbnail : '/assets/img/blog/blog-s-1-' . ($event->id % 3 + 1) . '.jpg',
                'link' => route('events.detail', $event),
                'category' => $event->category ? $event->category->name : 'Technology',
                'start_date' => $event->start_date,
                'organizer' => $event->organizerInfo ? $event->organizerInfo->name : ($event->organizer ?? 'ACTiV Team'),
                'days_remaining' => (int) $daysRemaining,
                'is_finished' => $isFinished,
            ];
        };

        $upcomingEvents = Event::with(['category', 'organizerInfo'])
            ->where('is_active', true)
            ->where('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->take(3)
            ->get()
            ->map($formatEvent);

        $pastEvents = Event::with(['category', 'organizerInfo'])
            ->where('is_active', true)
            ->where('start_date', '<', now())
            ->orderBy('start_date', 'desc')
            ->take(3)
            ->get()
            ->map($formatEvent);

        return Inertia::render('Events/Index', [
            'upcomingEvents' => $upcomingEvents,
            'pastEvents' => $pastEvents,
        ]);
    }
    public function show(Event $event)
    {
        if (!$event->is_active) {
            abort(404);
        }
        // Calculate status and days remaining
        $daysRemaining = round(now()->diffInDays($event->start_date, false));
        $isFinished = $daysRemaining < 0;
        $statusLabel = $isFinished ? 'Selesai' : ((int)$daysRemaining . ' Hari lagi');
        if ($daysRemaining == 0) {
            $statusLabel = 'Hari Ini';
        }

        $eventData = [
            'id' => $event->id,
            'title' => $event->title,
            'slug' => $event->slug,
            'description' => $event->description,
            'date' => [
                'day' => $event->start_date->format('d'),
                'month' => strtoupper($event->start_date->format('M')),
                'year' => $event->start_date->format('Y'),
                'full' => $event->start_date->format('F d, Y'),
            ],
            'time' => $event->start_date->format('h:i A') . ' - ' . ($event->end_date ? $event->end_date->format('h:i A') : 'End'),
            'start_time_full' => $event->start_date->format('d M Y H:i'),
            'end_time_full' => $event->end_date ? $event->end_date->format('d M Y H:i') : '-',
            'location' => $event->location,
            'price' => $event->price ?? 'Free',
            'quota' => $event->quota,
            'available_seats' => $event->available_seats,
            'image' => $event->thumbnail ? '/storage/' . $event->thumbnail : null,
            'category' => $event->category ? $event->category->name : 'Technology',
            'organizer' => $event->organizerInfo ? $event->organizerInfo->name : ($event->organizer ?? 'ACTiV Team'),
            'organizer_email' => $event->organizerInfo ? $event->organizerInfo->email : 'events@activ.com',
            'organizer_phone' => $event->organizerInfo ? $event->organizerInfo->phone : '+62 812 3456 7890',
            'organizer_logo' => $event->organizerInfo && $event->organizerInfo->logo ? '/storage/' . $event->organizerInfo->logo : null,
            'map_url' => $event->map_url,
            'youtube_link' => $event->youtube_link,
            'meeting_link' => $event->meeting_link, // Needed for location logic
            'schedule' => $event->schedule ?? [],
            'faq' => $event->faq ?? [],
            'speakers' => collect($event->speakers ?? [])->map(function ($speaker) {
                return [
                    'name' => $speaker['name'],
                    'role' => $speaker['role'],
                    'image' => isset($speaker['image']) ? '/storage/' . $speaker['image'] : null,
                    'linkedin_link' => $speaker['linkedin_link'] ?? null,
                    'instagram_link' => $speaker['instagram_link'] ?? null,
                    'tiktok_link' => $speaker['tiktok_link'] ?? null,
                ];
            }),
            'documentations' => $event->documentations->map(function ($doc) {
                // Handle file_path which is now an array (or null)
                $paths = $doc->file_path;
                if (is_string($paths)) {
                     // Legacy or single string fallback
                     $paths = [$paths];
                }
                if (!is_array($paths)) {
                    $paths = [];
                }

                // Map paths to full URLs if they are files, keep as is if video links
                $formattedPaths = array_map(function($path) use ($doc) {
                    if ($doc->type === 'video_link') {
                        return $path;
                    }
                    return '/storage/' . $path;
                }, $paths);

                return [
                    'id' => $doc->id,
                    'type' => $doc->type,
                    'file_paths' => $formattedPaths, // Send array of paths
                    'caption' => $doc->caption,
                ];
            }),
            'status_label' => $statusLabel,
            'is_finished' => $isFinished,
        ];

        return Inertia::render('Events/EventDetail', [
            'event' => $eventData,
        ]);
    }
    public function list_page()
    {
        $events = Event::with(['category', 'organizerInfo'])
            ->where('is_active', true)
            ->orderBy('start_date', 'asc')
            ->paginate(12)
            ->through(function ($event) {
                // Calculate status
                $daysRemaining = round(now()->diffInDays($event->start_date, false));
                $isFinished = $daysRemaining < 0;
                $statusLabel = $isFinished ? 'Selesai' : ((int)$daysRemaining . ' Hari lagi');
                if ($daysRemaining == 0) {
                    $statusLabel = 'Hari Ini';
                }

                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'slug' => $event->slug,
                    'description' => Str::limit(strip_tags($event->description, '<strong>'), 150),
                    'date' => [
                        'day' => $event->start_date->format('d'),
                        'month' => strtoupper($event->start_date->format('M')),
                        'year' => $event->start_date->format('Y'),
                    ],
                    'time' => $event->start_date->format('h:i A') . ' - ' . ($event->end_date ? $event->end_date->format('h:i A') : 'End'),
                    'location' => $event->location,
                    'price' => $event->price ?? ($event->id % 3 === 0 ? 'Free' : '$' . ($event->id * 10 + 50)), // Fallback to dummy if null
                    'quota' => $event->quota,
                    'available_seats' => $event->available_seats,
                    'image' => $event->thumbnail ? '/storage/' . $event->thumbnail : '/assets/img/blog/blog-s-1-' . ($event->id % 3 + 1) . '.jpg',
                    'link' => '/events/' . $event->slug,
                    'category' => $event->category ? $event->category->name : (['Business', 'Technology', 'Education', 'Social'][$event->id % 4] ?? 'Technology'),
                    'start_date' => $event->start_date, // Added for sorting logic in frontend
                    'date_full' => $event->start_date->format('Y-m-d H:i:s'),
                    'organizer' => $event->organizerInfo ? $event->organizerInfo->name : ($event->organizer ?? 'ACTiV Team'),
                    'status_label' => $statusLabel,
                    'is_finished' => $isFinished,
                    'days_remaining' => (int)$daysRemaining,
                ];
            });

        $categories = EventCategory::orderBy('name')->pluck('name');

        return Inertia::render('Events/EventList', [
            'events' => $events,
            'categories' => $categories,
        ]);
    }
}
