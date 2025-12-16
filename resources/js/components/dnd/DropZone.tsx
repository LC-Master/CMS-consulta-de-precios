import React from 'react';
import { useDroppable } from '@dnd-kit/react';
import { CollisionPriority } from '@dnd-kit/abstract';

export default function DropZone({ id, children }: { id: string; children?: React.ReactNode; }) {
    const { ref } = useDroppable({
        id,
        type: 'column',
        accept: ['item'],
        collisionPriority: CollisionPriority.Low,
    });

    return (
        <div className='flex flex-col gap-1 p-2 min-w-2 bg-gray-500 border-r' ref={ref}>
            {children}
        </div>
    );
}       