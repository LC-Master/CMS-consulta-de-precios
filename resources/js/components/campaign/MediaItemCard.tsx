import { cdn } from "@/routes/media";
import { cdn as thumbnailCDN } from "@/routes/thumbnail"
import { MediaItem } from "@/types/campaign/index.types";
import { ReactNode } from "react";
import { useState } from "react";
export default function MediaItemCard({ item, controls }: { item: MediaItem; controls: ReactNode }) {
    const hasPreview = item.mime_type.startsWith('image') || !!item.thumbnail;
    const [error, setError] = useState(!hasPreview);
    return (
        <div className="p-3 mb-2 border rounded-md flex items-center justify-between gap-4 bg-white shadow-sm hover:border-blue-200 transition-colors">
            <div className="flex items-center gap-4 min-w-0">
                {!error ? (
                    <img
                        src={item.mime_type.startsWith('image')
                            ? cdn({ id: item.id }).url
                            : thumbnailCDN({ thumbnail: item.thumbnail! }).url
                        }
                        alt={item.name}
                        className="w-24 h-14 object-cover rounded-md shrink-0 bg-gray-50"
                        onError={() => setError(true)}
                    />
                ) : (
                    <div className="w-24 h-14 bg-gray-100 rounded-md flex flex-col items-center justify-center text-[10px] text-gray-400 shrink-0 border border-dashed border-gray-200">
                        <span>Sin vista previa</span>
                    </div>
                )}

                <div className="min-w-0">
                    <p className="font-medium text-sm text-gray-800 truncate" title={item.name}>
                        {item.name}
                    </p>
                    <p className="text-[10px] uppercase tracking-wider text-gray-400 mt-1 font-semibold">
                        {item.mime_type.split('/')[1]}
                    </p>
                </div>
            </div>

            <div className="flex items-center gap-2">
                {controls}
            </div>
        </div>
    );
}