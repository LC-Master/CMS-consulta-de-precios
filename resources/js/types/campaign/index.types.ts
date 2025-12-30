import { StatusCampaignEnum } from "@/enums/statusCampaignEnum";
import { Agreement } from "../agreement/index.types";

export interface Department {
    id: string;
    name: string;
}
export interface Center extends Pick<Department, 'id' | 'name'> {
    code: string;
}
export interface MediaItem extends Pick<Department, 'id' | 'name'> {
    size: string;
    duration_seconds: string;
    slot?: 'am' | 'pm';
    campaigns?: Campaign[];
    position?: string;
    mime_type: 'image/jpeg' | 'video/mp4';
    thumbnails?: {
        id: string;
        media_id: string;
    } | null;
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

export type MediaPivot = {
    campaign_id: string;
    media_id: string;
    position: string;
    slot: "am" | "pm";
    created_at: string;
    updated_at: string;
};


export type Media = {
    id: string;
    name: string;
    mime_type: string;
    duration_seconds: string | null;
    created_at: string;
    updated_at: string;
    pivot: MediaPivot;
};


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

export type Campaign = {
    id: string;
    title: string;
    status: {
        status: typeof StatusCampaignEnum[keyof typeof StatusCampaignEnum];
    };
    created_at: string;
}

export type CampaignExtended = Campaign & {
    agreement: Agreement;
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
    flash?: { success?: string; error?: string };
    campaigns: { data: Campaign[] };
    filters: { search?: string; status?: string; type?: string };
    statuses: Status[];
    medias?: { data: MediaItem[] };
    mimeTypes?: string[];
};
