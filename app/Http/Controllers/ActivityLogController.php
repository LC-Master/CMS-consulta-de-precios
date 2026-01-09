<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = ActivityLog::with('user:id,name')
            ->orderBy('created_at', 'desc');

        if ($user->hasRole('publicidad')) {
            $query->where('subject_type', \App\Models\Campaign::class);
        }

        if ($request->has('element')) {
            $element = $request->input('element');
            $subjectClass = match ($element) {
                'campaign' => \App\Models\Campaign::class,
                'user' => $user->hasRole('admin') ? \App\Models\User::class : null,
                'center' => $user->hasRole('admin') ? \App\Models\Center::class : null,
                default => null,
            };

            if ($subjectClass) {
                $query->where('subject_type', $subjectClass);
            }
        }
        $elements = [
            'campaign' => 'campañas',
        ];
        if ($user->hasRole('admin')) {
            $elements['user'] = 'usuarios';
            $elements['center'] = 'tokens de centro';
        }
        if ($request->has('search')) {
            $query->where('message', 'like', "%{$request->input('search')}%");
        }

        return Inertia::render('Logs/Index', [
            'logs' => Inertia::scroll(
                fn() =>
                $query->paginate(20)->through(fn($log) => [
                    'id' => $log->id,
                    'action' => $log->action,
                    'level' => $log->level,
                    'message' => $log->message,
                    'user_agent' => $log->user_agent,
                    'properties' => $log->properties,
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at,
                    'user' => $log->user?->only(['id', 'name']),
                    'subject_type' => class_basename($log->subject_type),
                    'subject_id' => $log->subject_id,
                ])
            ),
            'filters' => $request->only(['search', 'element']),
            'elements' => $elements,
        ]);
    }
}