import { DndContext, closestCenter, useDroppable, useDraggable } from '@dnd-kit/core'
import { useState } from 'react'
import { X } from 'lucide-react'

function DropZone({ id, items, onRemove }: { id: string; items: any[]; onRemove: (itemId: string) => void }) {
    const { setNodeRef, isOver } = useDroppable({ id })

    return (
        <div className='w-full h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center' ref={setNodeRef}>
            {isOver ? 'suelta aqui' : 'Arrastra y suelta los elementos aqui'}
            {items.map((item) => (
                <div className='p-4 bg-gray-200 rounded mb-2 cursor-move'>
                    {item.type === 'image' ? '🖼️' : '🎬'} {item.name}
                    <button onClick={() => onRemove(item.id)}><X className='hover:transform-stroke '/></button>
                </div>
            ))}
        </div>
    )
}

function DraggableItem({ id, children }: { id: string; children: React.ReactNode }) {
    const { attributes, listeners, setNodeRef, transform, isDragging } = useDraggable({ id })
    return (
        <div
            ref={setNodeRef}
            style={{
                transform: transform ? `translate3d(${transform.x}px, ${transform.y}px, 0)` : undefined,
                opacity: isDragging ? 0.5 : 1,
            }}
            {...attributes}
            {...listeners}
        >
            {children}
        </div>
    )
}

export default function CampaignShow() {
    const [media, setMedia] = useState([{
        id: '12', type: 'image', name: 'Imagen 1', url: '', duration: 60
    }, {
        id: '32', type: 'video', name: 'Video 1', url: '', duration: 120
    }])
    const handleRemove = (itemId: string) => {
        const itemToMove = mediaZone.find(item => item.id === itemId)
        if (itemToMove) {
            setMedia([...media, itemToMove])
            setMediaZone(mediaZone.filter(item => item.id !== itemId))
        }
    }

    const [mediaZone, setMediaZone] = useState([])
    const handleDragEnd = (e) => {
        console.log(e, typeof (e))
        const { active, over } = e
        if (over && over.id === 'drop-zone-1') {
            const item = media.find(m => m.id === active.id)
            if (item) {
                setMediaZone([...mediaZone, item])
                setMedia(media.filter(m => m.id !== active.id))
            }
        }
    }
    return (
        <DndContext collisionDetection={closestCenter} onDragEnd={handleDragEnd}>
            <div className=''>
                <h1>Campaign Show</h1>
                <DropZone onRemove={handleRemove} id="drop-zone-1" items={mediaZone} />
                {media.map((item) => {
                    return <DraggableItem key={item.id} id={item.id}>
                        <div className='p-4 bg-gray-200 rounded mb-2 cursor-move'>
                            {item.type === 'image' ? '🖼️' : '🎬'} {item.name}
                        </div>
                    </DraggableItem>
                })}
            </div>
        </DndContext>
    )
}