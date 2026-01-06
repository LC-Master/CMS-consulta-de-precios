import { MediaItem } from "@/types/campaign/index.types";
import { ReactNode } from "react";

export default function MediaItemCard({ item, controls }: { item: MediaItem; controls: ReactNode }) {
    return (
        <div className="p-3 mb-2 border rounded-md flex items-center justify-between gap-4 bg-white shadow-sm">
            <div className="flex items-center gap-4 min-w-0">
                {item.mime_type.startsWith('image/') ? (
                    <img
                        src={`/media/cdn/${item.id}`}
                        alt={item.name}
                        className="w-24 h-14 object-cover rounded-md shrink-0" loading='lazy'
                    />
                ) : item.thumbnail ? (
                    <img
                        src={`/thumbnail/cdn/${item.thumbnail.id}`}
                        alt={`Thumbnail ${item.name}`}
                        className="w-24 h-14 object-cover rounded-md shrink-0" loading='lazy'
                    />
                ) : (
                    <div className="w-24 h-14 bg-gray-100 rounded-md flex items-center justify-center text-xs text-gray-500 shrink-0">
                        Sin vista previa
                    </div>
                )}

                <div className="min-w-0">
                    <p className="font-medium text-sm text-gray-800 truncate">{item.name}</p>
                    <p className="text-xs text-gray-500 mt-1">{item.mime_type}</p>
                </div>
            </div>

            <div className="flex items-center gap-2">
                {controls}
            </div>
        </div>
    )
}
