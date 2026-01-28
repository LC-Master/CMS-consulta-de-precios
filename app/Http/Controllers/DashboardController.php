<?php

namespace App\Http\Controllers;

use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Campaign;
use App\Models\Media;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = $this->gatherStats();

        return Inertia::render('dashboard', [
            'initialStats' => $stats,
        ]);
    }

    protected function gatherStats(): array
    {
        $now = Carbon::now();
        $start = $now->copy()->subMonths(5)->startOfMonth();

        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $months[] = $m->format('Y-m');
        }

        $campaignYmExpr = "CONCAT(YEAR(created_at), '-', RIGHT('0' + CAST(MONTH(created_at) AS varchar), 2))";
        $campaignsQuery = Campaign::select(DB::raw("{$campaignYmExpr} as ym"), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $start)
            ->groupBy(DB::raw($campaignYmExpr))
            ->pluck('count', 'ym')
            ->toArray();

        $campaignsPerMonth = array_map(function ($ym) use ($campaignsQuery) {
            return isset($campaignsQuery[$ym]) ? (int) $campaignsQuery[$ym] : 0;
        }, $months);

        $mediaYmExpr = $campaignYmExpr;
        $mediaQuery = Media::select(DB::raw("{$mediaYmExpr} as ym"), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $start)
            ->groupBy(DB::raw($mediaYmExpr))
            ->pluck('count', 'ym')
            ->toArray();

        $mediaPerMonth = array_map(function ($ym) use ($mediaQuery) {
            return isset($mediaQuery[$ym]) ? (int) $mediaQuery[$ym] : 0;
        }, $months);
        
        $active = Campaign::where('start_at', '<=', $now)->where('end_at', '>=', $now)->count();
        $pending = Campaign::where('start_at', '>', $now)->count();
        $finished = Campaign::where('end_at', '<', $now)->count();

        $avgDuration = Campaign::whereNotNull('end_at')->whereNotNull('start_at')
            ->select(DB::raw('AVG(CAST(DATEDIFF(day, start_at, end_at) AS FLOAT)) as avg_days'))
            ->value('avg_days');

        $topMedia = Media::select('mime_type', DB::raw('COUNT(*) as count'))
            ->groupBy('mime_type')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn($m) => ['mime_type' => $m->mime_type, 'count' => (int) $m->count])
            ->toArray();

        $recent = Campaign::with('user')->orderByDesc('created_at')->limit(5)->get(['id', 'title', 'created_at', 'start_at', 'end_at', 'user_id'])
            ->map(fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'created_at' => $c->created_at->toDateTimeString(),
                'start_at' => $c->start_at?->toDateTimeString(),
                'end_at' => $c->end_at?->toDateTimeString(),
                'user' => $c->user?->name,
            ])->toArray();

        $totals = [
            'campaigns_total' => Campaign::count('*'),
            'campaigns_deleted' => Campaign::onlyTrashed()->count(),
            'media_total' => Media::count('*'),
            'users_total' => User::count('*'),
            'campaigns_active' => $active,
            'campaigns_pending' => $pending,
            'campaigns_finished' => $finished,
            'avg_campaign_days' => $avgDuration ? (float) $avgDuration : 0,
        ];


        return [
            'labels' => $months,
            'campaigns' => $campaignsPerMonth,
            'media' => $mediaPerMonth,
            'totals' => $totals,
            'top_media' => $topMedia,
            'recent_campaigns' => $recent,
        ];
    }
}
