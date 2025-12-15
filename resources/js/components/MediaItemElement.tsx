import { TypeMediaItemElement } from "@/types/campaign/index.types";
import { XCircle } from 'lucide-react'
import { useSortable } from '@dnd-kit/sortable';
import { CSS } from "@dnd-kit/utilities";

export default function MediaItemElement({ item, index, column, onRemove }: TypeMediaItemElement) {
    const {
        attributes,
        listeners,
        setNodeRef,
        transform,
        transition,
        isDragging,
    } = useSortable({
        id: item.id,
        data: {
            type: "item",
            item: item,
        },
    });

    const style = {
        transform: CSS.Transform.toString(transform),
        transition,
    };


    return (
        <div
            ref={setNodeRef}
            style={style}
            {...attributes}
            {...listeners}
            key={item.id}
            className={`p-2 bg-white border border-gray-200 rounded mb-2 flex justify-between items-center shadow-sm ${isDragging ? "opacity-50 cursor-grabbing" : "opacity-100 cursor-grab"}`}
        >
            <span className="flex items-center gap-2">
                {item.mime_type === "image/jpeg" ? "🖼️" : "🎞️"} {item.name}
            </span>
            {
                onRemove && (
                    <button
                        type="button"
                        className="text-red-500 hover:text-red-700 font-bold px-2" onClick={() => onRemove(item.id)}>
                        <XCircle />
                    </button>
                )
            }
        </div >
    )
}