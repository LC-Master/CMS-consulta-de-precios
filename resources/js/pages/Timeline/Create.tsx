import React, { useState, useRef } from 'react';
import { Upload, X, Trash2, GripVertical, Image as ImageIcon, Video, Save } from 'lucide-react';
import { DndProvider, useDrag, useDrop, DropTargetMonitor } from 'react-dnd';
import { HTML5Backend } from 'react-dnd-html5-backend';
import AppLayout from '@/layouts/app-layout';
import { router } from '@inertiajs/react';

const GREEN_PRIMARY = '#007853';
const elements = [
    { id: 'lib-1', type: 'video', name: 'Intro_Comercial.mp4', duration: 60, color: GREEN_PRIMARY },
    { id: 'lib-2', type: 'image', name: 'Banner_Promo.jpg', duration: 60, color: GREEN_PRIMARY },
    { id: 'lib-3', type: 'video', name: 'Entrevista_CEO.mp4', duration: 120, color: GREEN_PRIMARY },
]
const ItemTypes = {
    MEDIA: 'media',
};

const formatTime = (totalMinutes: number) => {
    const hours = Math.floor(totalMinutes / 60);
    const minutes = Math.floor(totalMinutes % 60);
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const displayHours = hours % 12 || 12;
    return `${displayHours}:${minutes.toString().padStart(2, '0')} ${ampm}`;
};

interface LibraryItem {
    id: string;
    type: string;
    name: string;
    duration: number;
    color: string;
    url?: string;
    file?: File;
}

interface TimelineItem {
    id: string;
    mediaId: string;
    name: string;
    type: string;
    start: number;
    duration: number;
    color: string;
}

interface DraggingItem extends LibraryItem {
    source: 'library' | 'timeline';
    start?: number; // Optional because library items don't have it
}

const DraggableLibraryItem = ({ item, onRemove }: { item: LibraryItem; onRemove: (id: string) => void }) => {
    const [{ isDragging }, drag] = useDrag(() => ({
        type: ItemTypes.MEDIA,
        item: { ...item, source: 'library' },
        collect: (monitor) => ({
            isDragging: !!monitor.isDragging(),
        }),
    }));

    return (
        <div
            ref={(node) => { drag(node); }}
            className={`relative p-3 rounded-lg border border-gray-200 shadow-sm cursor-grab active:cursor-grabbing hover:shadow-md transition-all group bg-white hover:border-[#007853] ${isDragging ? 'opacity-50' : ''}`}
        >
            <div className="flex gap-3 items-center">
                {/* Thumbnail Placeholder */}
                <div
                    className="w-16 h-12 rounded flex items-center justify-center shrink-0"
                    style={{ backgroundColor: GREEN_PRIMARY }}
                >
                    {item.type === 'video' ?
                        <Video className="text-white w-6 h-6" /> :
                        <ImageIcon className="text-white w-6 h-6" />
                    }
                </div>
                <div className="overflow-hidden">
                    <p className="text-sm font-medium text-gray-800 truncate">{item.name}</p>
                    <span className="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full inline-block mt-1 uppercase">
                        {item.type}
                    </span>
                </div>
            </div>

            {/* Delete Button */}
            <button
                onClick={() => onRemove(item.id)}
                className="absolute top-2 right-2 p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
            >
                <Trash2 size={14} />
            </button>
        </div>
    );
};

const DraggableTimelineItem = ({
    item,
    pixelsPerMinute,
    onResize,
    onRemove,
    isGhost = false
}: {
    item: TimelineItem;
    pixelsPerMinute: number;
    onResize?: (e: React.MouseEvent, item: TimelineItem, direction: 'left' | 'right') => void;
    onRemove?: (id: string) => void;
    isGhost?: boolean;
}) => {
    const [{ isDragging }, drag] = useDrag(() => ({
        type: ItemTypes.MEDIA,
        item: { ...item, source: 'timeline' },
        collect: (monitor) => ({
            isDragging: !!monitor.isDragging(),
        }),
    }), [item]);

    if (isGhost) {
        return (
            <div
                className="absolute top-1/2 -translate-y-1/2 h-24 rounded-lg border-2 border-dashed border-white/50 bg-white/10 z-0 pointer-events-none"
                style={{
                    left: `${item.start * pixelsPerMinute}px`,
                    width: `${item.duration * pixelsPerMinute}px`,
                }}
            />
        );
    }

    return (
        <div
            ref={(node) => { drag(node); }}
            className={`absolute top-1/2 -translate-y-1/2 h-24 rounded-lg shadow-lg group overflow-hidden border border-white/20 hover:z-20 transition-colors ${isDragging ? 'opacity-0' : ''}`}
            style={{
                left: `${item.start * pixelsPerMinute}px`,
                width: `${item.duration * pixelsPerMinute}px`,
                backgroundColor: item.color,
                cursor: 'grab'
            }}
        >
            {/* Content */}
            <div className="p-2 h-full flex flex-col justify-between relative">
                <div className="flex justify-between items-start">
                    <div className="flex items-center gap-1 text-white text-xs font-bold truncate pr-6">
                        {item.type === 'video' ? <Video size={12} /> : <ImageIcon size={12} />}
                        <span className="truncate">{item.name}</span>
                    </div>
                    {onRemove && (
                        <button
                            onClick={(e) => { e.stopPropagation(); onRemove(item.id); }}
                            className="text-white/70 hover:text-white hover:bg-black/20 rounded p-0.5 absolute top-1 right-1"
                        >
                            <X size={14} />
                        </button>
                    )}
                </div>
                <div className="text-white/80 text-[10px] font-mono mt-1">
                    {formatTime(item.start)} - {formatTime(item.start + item.duration)}
                </div>
            </div>

            {/* Drag Handle (Left Edge) for Resizing */}
            {onResize && (
                <div
                    className="absolute left-0 top-0 bottom-0 w-4 cursor-col-resize hover:bg-white/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-10"
                    onMouseDown={(e) => onResize(e, item, 'left')}
                >
                    <GripVertical size={12} className="text-white" />
                </div>
            )}

            {/* Drag Handle (Right Edge) for Resizing */}
            {onResize && (
                <div
                    className="absolute right-0 top-0 bottom-0 w-4 cursor-col-resize hover:bg-white/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-10"
                    onMouseDown={(e) => onResize(e, item, 'right')}
                >
                    <GripVertical size={12} className="text-white" />
                </div>
            )}
        </div>
    );
};

function TimelineEditor({ initialTimeline = [], initialLibrary = [] }: { initialTimeline?: TimelineItem[], initialLibrary?: LibraryItem[] }) {
    const [libraryItems, setLibraryItems] = useState<LibraryItem[]>(initialLibrary.length > 0 ? initialLibrary : elements);

    const [timelineItems, setTimelineItems] = useState<TimelineItem[]>(initialTimeline);
    const [previewItem, setPreviewItem] = useState<TimelineItem | null>(null);

    const timelineRef = useRef<HTMLDivElement>(null);
    const scrollContainerRef = useRef<HTMLDivElement>(null);

    const PIXELS_PER_MINUTE = 2;
    const TOTAL_HOURS = 24;
    const SNAP_MINUTES = 5;

    const saveTimeline = () => {
        // 1. Preparar el payload del timeline
        const timelinePayload = timelineItems.map(item => ({
            mediaId: item.mediaId,
            // Si el mediaId empieza con 'new-', es un archivo subido en esta sesión
            tempId: item.mediaId.startsWith('new-') ? item.mediaId : null,
            start: item.start,
            duration: item.duration,
            type: item.type,
            name: item.name
        }));

        // 2. Preparar los archivos nuevos
        const filesMap: Record<string, File> = {};
        libraryItems.forEach(item => {
            if (item.file) {
                filesMap[item.id] = item.file;
            }
        });
        console.log(filesMap,timelinePayload)
        // 3. Enviar al backend (Inertia convertirá esto a FormData automáticamente)
        router.post('/timeline/save', {
            timeline: timelinePayload,
            files: filesMap
        }, {
            forceFormData: true,
            onSuccess: () => alert('¡Programación guardada con éxito!'),
            onError: (errors) => {
                console.error('Error al guardar:', errors);
                alert('Hubo un error al guardar la programación.');
            },
        });
    };

    const hasCollision = (newItem: TimelineItem, currentItems: TimelineItem[], excludeId: string | null = null) => {
        const newEnd = newItem.start + newItem.duration;
        return currentItems.some(item => {
            if (item.id === excludeId) return false;
            const itemEnd = item.start + item.duration;
            return (newItem.start < itemEnd && newEnd > item.start);
        });
    };

    const addTimelineItem = (newItem: TimelineItem) => {
        if (!hasCollision(newItem, timelineItems)) {
            setTimelineItems(prev => [...prev, newItem]);
        }
    };

    const calculateDropPosition = (item: DraggingItem, monitor: DropTargetMonitor) => {
        if (!timelineRef.current || !scrollContainerRef.current) return null;

        let startMinute = 0;

        if (item.source === 'timeline') {
            const delta = monitor.getDifferenceFromInitialOffset();
            if (!delta) return item.start;

            const deltaMinutes = Math.round((delta.x / PIXELS_PER_MINUTE) / SNAP_MINUTES) * SNAP_MINUTES;
            startMinute = item.start + deltaMinutes;
        } else {
            const clientOffset = monitor.getClientOffset();
            if (!clientOffset) return null;

            const rect = timelineRef.current.getBoundingClientRect();
            const scrollLeft = scrollContainerRef.current.scrollLeft;
            const clickX = (clientOffset.x - rect.left) + scrollLeft;

            startMinute = Math.floor(clickX / PIXELS_PER_MINUTE);
            startMinute = Math.round(startMinute / SNAP_MINUTES) * SNAP_MINUTES;
        }

        return Math.max(0, startMinute);
    };

    const [{ isOver }, drop] = useDrop(() => ({
        accept: ItemTypes.MEDIA,
        hover: (item: DraggingItem, monitor) => {
            const newStart = calculateDropPosition(item, monitor);
            if (newStart === null) return;

            const duration = item.duration || 60;
            const tempItem: TimelineItem = {
                id: item.source === 'timeline' ? item.id : 'preview',
                mediaId: item.id,
                name: item.name,
                type: item.type,
                start: newStart,
                duration: duration,
                color: item.color
            };

            if (!hasCollision(tempItem, timelineItems, item.source === 'timeline' ? item.id : null)) {
                setPreviewItem(tempItem);
            } else {
                setPreviewItem(null);
            }
        },
        drop: (item: DraggingItem, monitor) => {
            const newStart = calculateDropPosition(item, monitor);
            setPreviewItem(null); // Clear ghost

            if (newStart === null) return;

            if (item.source === 'library') {
                const newItem: TimelineItem = {
                    id: `tl-${new Date().getTime()}`,
                    mediaId: item.id,
                    name: item.name,
                    type: item.type,
                    start: newStart,
                    duration: item.duration || 60,
                    color: item.color
                };
                addTimelineItem(newItem);
            } else if (item.source === 'timeline') {
                const movedItem = { ...item, start: newStart } as TimelineItem;
                if (!hasCollision(movedItem, timelineItems, item.id)) {
                    setTimelineItems(prev => prev.map(i => i.id === item.id ? { ...i, start: newStart } : i));
                }
            }
        },
        collect: (monitor) => ({
            isOver: !!monitor.isOver(),
        }),
    }), [timelineItems]);

    // --- Lógica de Carga de Archivos ---
    const handleFileUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
        const files = Array.from(e.target.files || []);
        const newItems: LibraryItem[] = files.map((file, index) => ({
            id: `new-${new Date().getTime()}-${index}`,
            type: file.type.startsWith('video') ? 'video' : 'image',
            name: file.name,
            duration: 60,
            url: URL.createObjectURL(file),
            color: GREEN_PRIMARY,
            file: file // Guardamos el archivo real para subirlo
        }));
        setLibraryItems([...libraryItems, ...newItems]);
    };

    // --- Lógica de Redimensionado (Estirar) ---
    const handleResizeStart = (e: React.MouseEvent, item: TimelineItem, direction: 'left' | 'right') => {
        e.stopPropagation();
        e.preventDefault();

        const startX = e.clientX;
        const originalStart = item.start;
        const originalDuration = item.duration;

        const onMouseMove = (moveEvent: MouseEvent) => {
            const deltaX = moveEvent.clientX - startX;
            const deltaMinutes = Math.floor(deltaX / PIXELS_PER_MINUTE);

            if (direction === 'right') {
                let newDuration = Math.max(5, originalDuration + deltaMinutes);
                newDuration = Math.round(newDuration / SNAP_MINUTES) * SNAP_MINUTES;
                const tempItem = { ...item, duration: newDuration };

                if (!hasCollision(tempItem, timelineItems, item.id)) {
                    setTimelineItems(prev => prev.map(i => i.id === item.id ? tempItem : i));
                }
            } else {
                let newStart = originalStart + deltaMinutes;
                newStart = Math.round(newStart / SNAP_MINUTES) * SNAP_MINUTES;
                const originalEnd = originalStart + originalDuration;
                const newDuration = originalEnd - newStart;

                if (newDuration >= 5 && newStart >= 0) {
                    const tempItem = { ...item, start: newStart, duration: newDuration };
                    if (!hasCollision(tempItem, timelineItems, item.id)) {
                        setTimelineItems(prev => prev.map(i => i.id === item.id ? tempItem : i));
                    }
                }
            }
        };

        const onMouseUp = () => {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        };

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    };

    const removeTimelineItem = (id: string) => {
        setTimelineItems(prev => prev.filter(i => i.id !== id));
    };

    const removeLibraryItem = (id: string) => {
        setLibraryItems(prev => prev.filter(i => i.id !== id));
    };

    return (
        <div className="flex flex-col h-screen bg-gray-50 font-sans text-gray-800">

            {/* MAIN CONTENT */}
            <div className="flex-1 flex flex-col overflow-hidden">

                {/* TOP: TIMELINE AREA */}
                <div className="h-1/3 min-h-[250px] bg-gray-900 border-b relative flex flex-col shadow-inner">
                    <div className="absolute top-2 left-4 z-10 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-80 pointer-events-none">
                        Timeline (24 Horas)
                    </div>

                    {/* Scroll Container */}
                    <div
                        ref={(node) => {
                            drop(node);
                            scrollContainerRef.current = node;
                        }}
                        className={`flex-1 overflow-x-auto overflow-y-hidden relative custom-scrollbar ${isOver ? 'bg-gray-800/50' : ''}`}
                    >
                        <div
                            ref={timelineRef}
                            className="relative h-full"
                            style={{ width: `${TOTAL_HOURS * 60 * PIXELS_PER_MINUTE}px` }}
                        >
                            {/* Grid Background & Time Labels */}
                            {Array.from({ length: TOTAL_HOURS }).map((_, i) => (
                                <div
                                    key={i}
                                    className="absolute top-0 bottom-0 border-l border-gray-700/50"
                                    style={{ left: `${i * 60 * PIXELS_PER_MINUTE}px` }}
                                >
                                    <span className="absolute top-2 left-2 text-gray-400 text-sm font-mono select-none">
                                        {formatTime(i * 60)}
                                    </span>
                                </div>
                            ))}

                            {/* Preview Ghost Item */}
                            {previewItem && (
                                <DraggableTimelineItem
                                    item={previewItem}
                                    pixelsPerMinute={PIXELS_PER_MINUTE}
                                    isGhost={true}
                                />
                            )}

                            {/* Timeline Items */}
                            {timelineItems.map((item) => (
                                <DraggableTimelineItem
                                    key={item.id}
                                    item={item}
                                    pixelsPerMinute={PIXELS_PER_MINUTE}
                                    onResize={handleResizeStart}
                                    onRemove={removeTimelineItem}
                                />
                            ))}
                        </div>
                    </div>
                </div>

                {/* BOTTOM AREA */}
                <div className="flex-1 flex overflow-hidden">

                    {/* LEFT: MEDIA LIBRARY */}
                    <div className="w-1/3 border-r bg-white flex flex-col min-w-[300px]">
                        <div className="p-4 border-b bg-gray-50">
                            <h2 className="font-semibold text-gray-700 flex items-center gap-2">
                                <ImageIcon className="w-4 h-4" /> Librería de Medios
                            </h2>
                            <p className="text-xs text-gray-500 mt-1">Arrastra elementos al timeline superior.</p>
                        </div>

                        <div className="flex-1 overflow-y-auto p-4 space-y-3">
                            {libraryItems.length === 0 && (
                                <div className="text-center py-10 text-gray-400 text-sm italic">
                                    No hay videos o imágenes.<br />Súbelos en el panel derecho.
                                </div>
                            )}
                            {libraryItems.map((item) => (
                                <DraggableLibraryItem
                                    key={item.id}
                                    item={item}
                                    onRemove={removeLibraryItem}
                                />
                            ))}
                        </div>
                    </div>

                    {/* RIGHT: UPLOAD AREA */}
                    <div className="w-2/3 bg-gray-50 p-8 flex flex-col justify-center items-center">
                        <div className="w-full max-w-md">
                            <label
                                className="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-white hover:bg-gray-50 hover:border-[#007853] transition-colors group"
                            >
                                <div className="flex flex-col items-center justify-center pt-5 pb-6">
                                    <div className="mb-4 p-4 rounded-full bg-gray-50 group-hover:bg-[#007853]/10 transition-colors">
                                        <Upload className="w-10 h-10 text-gray-400 group-hover:text-[#007853]" />
                                    </div>
                                    <p className="mb-2 text-sm text-gray-500 font-medium">Haz clic o arrastra archivos aquí</p>
                                    <p className="text-xs text-gray-400">Soporta: MP4, AVI, JPG, PNG</p>
                                </div>
                                <input
                                    type="file"
                                    className="hidden"
                                    multiple
                                    accept="image/*,video/*"
                                    onChange={handleFileUpload}
                                />
                            </label>

                            <div className="mt-6 flex gap-4 text-xs text-gray-400 justify-center">
                                <span className="flex items-center gap-1"><div className="w-2 h-2 rounded-full bg-[#007853]"></div> Videos permitidos</span>
                                <span className="flex items-center gap-1"><div className="w-2 h-2 rounded-full bg-[#007853]"></div> Imágenes permitidas</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


        </div>
    );
}

export default function TimeLineCreate({ initialTimeline, initialLibrary }: { initialTimeline?: TimelineItem[], initialLibrary?: LibraryItem[] }) {
    return (
        <AppLayout>
            <DndProvider backend={HTML5Backend}>
                <TimelineEditor initialTimeline={initialTimeline} initialLibrary={initialLibrary} />
            </DndProvider>
        </AppLayout>
    );
}