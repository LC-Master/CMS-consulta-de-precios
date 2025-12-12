import {
    DragDropProvider,
    useDroppable,
    useDraggable,
} from "@dnd-kit/react";
import { useState } from "react";

interface MediaItem {
    id: string;
    type: "image" | "video";
    name: string;
}

interface DropZoneProps {
    id: string;
    items: MediaItem[];
    onRemove: (id: string) => void;
}

function DropZone({ id, items, onRemove }: DropZoneProps) {
    const { ref, isDropTarget } = useDroppable({ id });

    return (
        <div
            ref={ref}
            className={`border p-4 min-h-[200px] transition-colors ${isDropTarget ? "bg-green-100 border-green-500" : "bg-gray-100 border-gray-300"
                }`}
        >
            <h3 className="mb-2 font-semibold text-gray-700">
                {isDropTarget ? "¡Suelta aquí!" : "Zona de Medios"}
            </h3>

            {items.map((item) => (
                <div
                    key={item.id}
                    className="p-2 bg-white border border-gray-200 rounded mb-2 flex justify-between items-center shadow-sm"
                >
                    <span className="flex items-center gap-2">
                        {item.type === "image" ? "🖼️" : "🎬"} {item.name}
                    </span>

                    <button
                        className="text-red-500 hover:text-red-700 font-bold px-2"
                        onClick={() => onRemove(item.id)}
                    >
                        ✕
                    </button>
                </div>
            ))}
            {items.length === 0 && !isDropTarget && (
                <p className="text-gray-400 text-sm text-center mt-4">Arrastra elementos aquí</p>
            )}
        </div>
    );
}

interface DraggableItemProps {
    item: MediaItem;
}

function DraggableItem({ item }: DraggableItemProps) {
    const { ref, isDragging } = useDraggable({ id: item.id });

    return (
        <div
            ref={ref}
            className={`p-3 bg-white border border-gray-200 rounded mb-2 cursor-move shadow-sm hover:shadow-md transition-all flex items-center gap-2 ${isDragging ? "opacity-50" : "opacity-100"
                }`}
        >
            {item.type === "image" ? "🖼️" : "🎬"} {item.name}
        </div>
    );
}

interface DragEndEvent {
    operation: {
        source: { id: string | number } | null;
        target?: { id: string | number } | null;
    };
    canceled: boolean;
}

export default function CampaignShow() {
    const [media, setMedia] = useState<MediaItem[]>([
        { id: "12", type: "image", name: "Imagen 1" },
        { id: "32", type: "video", name: "Video 1" },
        { id: "45", type: "image", name: "Banner Principal" },
        { id: "56", type: "video", name: "Intro Comercial" },
    ]);
    const [mediaZone, setMediaZone] = useState<MediaItem[]>([]);

    const handleRemove = (itemId: string) => {
        const item = mediaZone.find((i) => i.id === itemId);
        if (!item) return;

        setMedia((prev) => [...prev, item]);
        setMediaZone((prev) => prev.filter((i) => i.id !== itemId));
    };

    const handleDragEnd = (event: DragEndEvent) => {
        const { operation, canceled } = event;

        if (canceled) return;

        const { source, target } = operation;

        if (!source || !target) return;

        // Verificar si el destino es la zona de drop
        if (target.id === "drop-zone-1") {
            const item = media.find((i) => i.id === source.id);
            if (!item) return;

            setMedia((prev) => prev.filter((i) => i.id !== source.id));
            setMediaZone((prev) => [...prev, item]);
        }
    };

    return (
        <div className="p-6 max-w-4xl mx-auto">
            <h1 className="text-2xl font-bold mb-6 text-gray-800">Gestión de Contenido de Campaña</h1>

            <DragDropProvider onDragEnd={handleDragEnd}>
                <div className="flex flex-col md:flex-row gap-6">
                    <div className="flex-1">
                        <h2 className="text-lg font-semibold mb-3 text-gray-700">Contenido Asignado</h2>
                        <DropZone
                            id="drop-zone-1"
                            items={mediaZone}
                            onRemove={handleRemove}
                        />
                    </div>

                    <div className="w-full md:w-72">
                        <h2 className="text-lg font-semibold mb-3 text-gray-700">Disponibles</h2>
                        <div className="bg-gray-50 p-4 rounded-lg border border-gray-200 min-h-[200px]">
                            {media.length === 0 ? (
                                <p className="text-gray-400 text-center text-sm">No hay medios disponibles</p>
                            ) : (
                                media.map((item) => (
                                    <DraggableItem key={item.id} item={item} />
                                ))
                            )}
                        </div>
                    </div>
                </div>
            </DragDropProvider>
        </div>
    );
}
