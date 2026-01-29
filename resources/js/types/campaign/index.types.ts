import { StatusCampaignEnum } from '@/enums/statusCampaignEnum';
import { Agreement } from '../agreement/index.types';
import { Status } from '../status/status.type';
import { Flash } from '../flash/flash.type';
import { Media, MediaItem } from '../media/index.type';

export type Department = {
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



export interface Center extends Pick<Department, 'id' | 'name'> {
    code: string;
}
export interface Option {
    value: string;
    label: string;
}

export type Campaign = {
    start_at: string | number | Date;
    end_at: string | number | Date;
    deleted_at?: string | null;
    id: string;
    title: string;
    status: {
        status: (typeof StatusCampaignEnum)[keyof typeof StatusCampaignEnum];
    };
    created_at: string;
};

export type CampaignExtended = Campaign & {
    agreements: Pick<Agreement, 'id' | 'name'>[];
    department: Department;
    centers: Center[];
    start_at: string;
    end_at: string;

    updated_at: string;

    created_by: string;
    updated_by: string | null;

    media: Media[];
};
export type Props = {
    flash?: Flash;
    campaigns: { data: Campaign[] };
    filters: {
        search?: string;
        status?: string;
        type?: string;
        ended_at?: string;
        started_at?: string;
    };
    statuses: Status[];
    medias?: { data: MediaItem[] };
    mimeTypes?: string[];
};
