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
        $isAdmin = auth()->user()->hasRole('admin');

        if ($request->has('element')) {
            $element = $request->input('element');
            $subjectClass = match ($element) {
                'campaign' => \App\Models\Campaign::class,
                'user' => \App\Models\User::class,
                'store' => \App\Models\Store::class,
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
            (object) ['value' => 'store', 'label' => 'tiendas'],
            (object) ['value' => 'agreement', 'label' => 'Acuerdo'],
            (object) ['value' => 'media', 'label' => 'Medios'],
            (object) ['value' => 'personalAccessToken', 'label' => 'Tokens de acceso'],
        ];

        if ($request->has('search') && $request->input('search') !== null) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search, $isAdmin) {
                $q->where('message', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('causer_id', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('user_email', 'like', "%{$search}%");

                if ($isAdmin) {
                    $q->orWhere('ip_address', 'like', "%{$search}%");
                }

                $q->orWhere('properties', 'like', "%{$search}%");
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
                    'user_agent' => $isAdmin ? $log->user_agent : null,
                    'properties' => \is_string($log->properties) ? json_decode($log->properties, true) : $log->properties,
                    'ip_address' => $isAdmin ? $log->ip_address : null,
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
