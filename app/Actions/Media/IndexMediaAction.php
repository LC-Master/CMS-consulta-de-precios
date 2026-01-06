<?php

namespace App\Actions\Media;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Media;
use App\Enums\MimeTypesEnum;
class IndexMediaAction
{
    /**
     * Create a new class instance.
     */
    public function list(Request $request)
    {
        $query = Media::query()->with([
            'campaigns' => function ($q) {
                $q->select('campaigns.*')->distinct();
            }
        ]);

        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('campaigns', function ($qCamp) use ($search) {
                        $qCamp->where('title', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $type = $request->input('type');
            if (\in_array($type, MimeTypesEnum::values())) {
                $query->where('mime_type', $type);
            }
        }

        $medias = Inertia::scroll(function () use ($query) {
            return $query->select('id', 'name', 'duration_seconds', 'mime_type', 'size')
                ->latest()
                ->paginate(20)
                ->withQueryString();
        });

        return $medias;
    }
}
