export interface Department {
    id: string;
    name: string;
}
export interface Center extends Pick<Department, 'id' | 'name'> {
    code: string;
}
export type Agreement = Department;

export interface MediaItem extends Pick<Department, 'id' | 'name'> {
    mime_type: 'image/jpeg' | 'video/mp4';
}

export interface Option {
    value: string;
    label: string;
}
export interface DraggableItemProps {
    item: MediaItem;
}
export interface TypeMediaItemElement {
    item: MediaItem;
    index: number;
    column: string;
    onRemove?: (id: string) => void;
    className?: string;
    dragOverlay?: boolean;
}

export interface DropZoneProps {
    id: string;
    items: MediaItem[];
    children?: React.ReactNode;
    className?: string;
}
export interface DragEndEvent {
    operation: {
        source: { id: string | number } | null;
        target?: { id: string | number } | null;
    };
    canceled: boolean;
}
export interface Department {
    id: string;
    name: string;
}
export interface Center extends Pick<Department, 'id' | 'name'> {
    code: string;
}
export interface Option {
    value: string;
    label: string;
}
export interface Status {
    id: string;
    status: string;
}

export interface Campaign {
    id: string;
    title: string;
    status: Status;
    created_at: string;
    [key: string]: unknown;
}

export interface Props {
    campaigns: { data: Campaign[] };
    filters: { search?: string; status?: string };
    statuses: Status[];
}