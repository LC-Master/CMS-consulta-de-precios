import { Campaign, Department } from "../campaign/index.types";

export type PreviewProps = {
    media: MediaItem;
    cdnUrl: string;
    imgRef: React.RefObject<HTMLImageElement | null>;
    videoRef: React.RefObject<HTMLVideoElement | null>;
    dimensions: { w: number; h: number } | null;
    loadError: boolean;
    onImageLoad: () => void;
    onMediaError: () => void;
    handleRetry: () => void;
};
export type Media = Pick<Department, 'id' | 'name'> & {
    mime_type: string;
    duration_seconds: string | null;
    created_at: string;
    updated_at: string;
    pivot: MediaPivot;
};
export type MediaPivot = {
    campaign_id: string;
    media_id: string;
    position: string;
    slot: 'am' | 'pm';
    created_at: string;
    updated_at: string;
};
export interface MediaItem extends Pick<Department, 'id' | 'name'> {
    size: string;
    instanceId?: string;
    duration_seconds: number | null;
    slot?: 'am' | 'pm';
    campaigns?: Campaign[];
    position?: string;
    mime_type: 'image/jpeg' | 'video/mp4';
    thumbnail?: {
        id: string;
        media_id: string;
    } | null;
}
