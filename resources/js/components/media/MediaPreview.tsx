import { PreviewProps } from "@/types/media/index.type";
import { MonitorX } from "lucide-react";
import MediaFallBack from "./MediaFallBack";
import MediaError from "./MediaError";

export default function MediaPreview({
    media,
    cdnUrl,
    imgRef,
    videoRef,
    dimensions,
    loadError,
    onImageLoad,
    onMediaError,
    handleRetry,
}: PreviewProps) {

    if (loadError) {
        return <MediaError onRetry={handleRetry} />;
    }

    if (media.mime_type?.startsWith('image/')) {
        return (
            <div className="w-full flex items-center justify-center min-h-64 md:min-h-[60vh] relative">
                {!dimensions && <MediaFallBack />}
                <img
                    key={cdnUrl}
                    ref={imgRef}
                    src={cdnUrl}
                    onLoad={onImageLoad}
                    onError={onMediaError}
                    alt={media.name}
                    className={`w-full h-auto max-h-[70vh] object-contain rounded transition-opacity duration-500 ${dimensions ? 'block opacity-100' : 'absolute opacity-0 pointer-events-none'
                        }`}
                />
            </div>
        );
    }

    if (media.mime_type?.startsWith('video/')) {
        return (
            <div className="w-full flex items-center justify-center min-h-64 md:min-h-[60vh] relative">
                {!dimensions && <MediaFallBack />}
                <video
                    key={cdnUrl}
                    ref={videoRef}
                    controls
                    preload="auto"
                    playsInline
                    onLoadedMetadata={onImageLoad}
                    onCanPlay={onImageLoad}
                    onError={onMediaError}
                    className={`w-full h-auto max-h-[70vh] rounded transition-opacity duration-500 ${dimensions ? 'block opacity-100' : 'absolute opacity-0 pointer-events-none'
                        }`}
                >
                    <source src={cdnUrl} type={media.mime_type} />
                </video>
            </div>
        );
    }

    return (
        <div className="flex flex-col items-center justify-center p-12">
            <div className="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mb-4 text-gray-400">
                <MonitorX size={40} />
            </div>
            <p className="text-center text-sm text-gray-600 font-medium">Vista previa no disponible.</p>
        </div>
    );
}