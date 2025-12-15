import { DropZoneProps } from "@/types/campaign/index.types";
import { useDroppable } from "@dnd-kit/core";

export default function DropZone({ id, items, children, className }: DropZoneProps) {
    const { setNodeRef, isOver } = useDroppable({
        id,
        data: {
            type: "container",
            children: items,
        }
    });
    return (
        <section
            ref={setNodeRef}
            className={`${className ?? ""} border p-4 min-h-[200px] max-h-80 overflow-y-auto overscroll-contain transition-colors ${isOver ? "bg-green-100 border-green-500" : "bg-gray-100 border-gray-300"
                }`}
            style={{ WebkitOverflowScrolling: 'touch' }}
        >
            <h3 className="text-lg font-bold mb-2 capitalize">{id}</h3>
            <div className="space-y-2">
                {children}
            </div>
            {isOver && (
                <p className="text-gray-400 text-sm text-center mt-4">Arrastra elementos aquí</p>
            )}
        </section>
    );
}       