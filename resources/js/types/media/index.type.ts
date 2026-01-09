import { MediaItem } from "../campaign/index.types";

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