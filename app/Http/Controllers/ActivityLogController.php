<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        if ($request->has('element')) {
            $element = $request->input('element');
            $subjectClass = match ($element) {
                'campaign' => \App\Models\Campaign::class,
                'user' => \App\Models\User::class,
                'center' => \App\Models\Center::class,
                'agreement' => \App\Models\Agreement::class,
                'media' => \App\Models\Media::class,
                'personalAccessToken' => \Laravel\Sanctum\PersonalAccessToken::class,
                default => null,
            };

            if ($subjectClass) {
                $query->where('subject_type', $subjectClass);
            }
        }

        $elements = [
            (object) ['value' => 'campaign', 'label' => 'campañas'],
            (object) ['value' => 'user', 'label' => 'usuarios'],
            (object) ['value' => 'center', 'label' => 'tokens de centro'],
            (object) ['value' => 'agreement', 'label' => 'Acuerdo'],
            (object) ['value' => 'media', 'label' => 'Medios'],
            (object) ['value' => 'personalAccessToken', 'label' => 'Tokens de acceso'],
        ];

        if ($request->has('search') && $request->input('search') !== null) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('properties->title', 'like', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        return Inertia::render('Logs/Index', [
            'logs' => Inertia::scroll(
                fn() =>
                $query->paginate(20)->through(fn($log) => [
                    'id' => $log->id,
                    'action' => $log->action,
                    'level' => $log->level,
                    'causer_id' => $log->causer_id,
                    'user_name' => $log->user_name,
                    'user_email' => $log->user_email,
                    'message' => $log->message,
                    'user_agent' => $log->user_agent,
                    'properties' => json_decode($log->properties, true),
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at,
                    'subject_type' => class_basename($log->subject_type),
                    'subject_id' => $log->subject_id,
                ])
            ),
            'filters' => $request->only(['search', 'element']),
            'elements' => $elements,
        ]);
    }
}
