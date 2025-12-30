import { useRef, useState } from 'react';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/media';
import { breadcrumbs } from '@/helpers/breadcrumbs';
import { MediaItem } from '@/types/campaign/index.types';
import { Link } from '@inertiajs/react';
import { mediaNameNormalizer, formatBytes } from '@/helpers/mediaTools';
import { CircleAlert, DownloadCloud, ArrowLeft, Book, ChevronRight } from 'lucide-react';
import { show } from '@/routes/campaign';
import MediaPreview from '@/components/media/MediaPreview';

export default function MediaShow({ media }: { media: MediaItem }) {
    const imgRef = useRef<HTMLImageElement | null>(null);
    const videoRef = useRef<HTMLVideoElement | null>(null);
    const [dimensions, setDimensions] = useState<{ w: number; h: number } | null>(null);
    const [loadError, setLoadError] = useState(false);
    const [retryCount, setRetryCount] = useState(0);

    const onImageLoad = () => {
        const img = imgRef.current;
        const video = videoRef.current;

        let w = 0;
        let h = 0;

        if (img && img.naturalWidth > 0) {
            w = img.naturalWidth;
            h = img.naturalHeight;
        } else if (video && video.videoWidth > 0) {
            w = video.videoWidth;
            h = video.videoHeight;
        }

        if (w > 0 && h > 0) {
            setDimensions({ w, h });
            setLoadError(false);
        }
    };

    const onMediaError = () => {
        setDimensions(null);
        setLoadError(true);
    };

    const handleRetry = () => {
        setLoadError(false);
        setDimensions(null);
        setRetryCount((r) => r + 1);

        setTimeout(() => {
            if (videoRef.current) {
                try {
                    videoRef.current.load();
                } catch {
                    /* ignore */
                }
            }
            if (imgRef.current) {
                const src = imgRef.current.src;
                imgRef.current.src = src;
            }
        }, 0);
    };

    const ratioText = (w: number, h: number) => `${Math.round((w / h) * 100) / 100}:1`;

    const cdnUrl = `/media/cdn/${media.id}${retryCount ? `?r=${retryCount}` : ''}`;

    return (
        <AppLayout breadcrumbs={breadcrumbs(`Detalle de medio - ${media.name}`, index().url)}>
            <div className="flex bg-gray-100 flex-col">
                <div className="space-y-2 ml-2 p-4">
                    <h1 className="text-3xl font-bold">Detalle de medio</h1>
                    <p className="text-sm">
                        Detalle y vista previa del archivo seleccionado. Revisa sus propiedades y usa las opciones para descargarlo o volver al listado.
                    </p>
                </div>

                <div className="flex flex-col md:flex-row ml-2 rounded-lg p-4 gap-4">
                    <div className="shadow rounded-lg p-4 w-full md:w-1/3 flex flex-col min-w-0 bg-white">
                        <div className="flex flex-col p-2 gap-3 mb-4">
                            <div className="flex flex-row items-center gap-2 mb-2">
                                <CircleAlert />
                                <h2 className="text-lg font-semibold">Información General</h2>
                            </div>

                            <div className="flex flex-row items-center pb-4 border-b border-gray-200 gap-2">
                                <Label className="text-gray-500 w-24 ">Nombre</Label>
                                <p className="text-sm font-bold  wrap-break-words">{mediaNameNormalizer(media.name)}</p>
                            </div>

                            <div className="flex flex-row items-center pb-4 border-b border-gray-200 gap-2">
                                <Label className="text-gray-500 w-24 ">Tipo:</Label>
                                <p className=" font-bold  bg-blue-200 text-sm rounded-lg p-1 text-blue-600">{media.mime_type}</p>
                            </div>

                            <div className="flex flex-row items-center pb-4 border-b border-gray-200 gap-2">
                                <Label className="text-gray-500 w-24 ">Tamaño:</Label>
                                <p className=" font-bold   text-sm">{formatBytes(media.size)}</p>
                            </div>

                            <div className="mt-6 flex flex-col gap-3">
                                <Label className="text-gray-500  text-xs font-medium uppercase">Campañas asociadas</Label>

                                <div className="mt-2">
                                    <div className="max-h-56 md:max-h-80 overflow-y-auto pr-2 space-y-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                                        {media.campaigns && media.campaigns.length > 0 ? (
                                            media.campaigns.map((campaign) => (
                                                <a
                                                    href={show({ id: campaign.id }).url}
                                                    key={campaign.id}
                                                    className="flex items-center justify-between gap-3 p-3 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:bg-gray-50 transition-colors cursor-pointer"
                                                >
                                                    <div className="flex items-center gap-3 min-w-0">
                                                        <div className="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-md text-gray-600 shrink-0">
                                                            <Book size={16} />
                                                        </div>

                                                        <div className="min-w-0">
                                                            <span className="block text-sm font-semibold text-gray-800 truncate">{campaign.title}</span>
                                                        </div>
                                                    </div>

                                                    <ChevronRight className="text-gray-400" />
                                                </a>
                                            ))
                                        ) : (
                                            <div className="flex items-center justify-center p-6 bg-white border border-dashed border-gray-200 rounded-lg">
                                                <div className="flex flex-col items-center text-center gap-3">
                                                    <div className="w-16 h-16 flex items-center justify-center rounded-full bg-gray-100 text-gray-500">
                                                        <Book size={20} />
                                                    </div>
                                                    <div>
                                                        <h4 className="text-sm font-semibold text-gray-800">No hay campañas asociadas</h4>
                                                        <p className="text-xs text-gray-500 mt-1">
                                                            Este medio aún no está vinculado a ninguna campaña. Puedes asociarlo desde la sección de campañas.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="mt-auto flex gap-2 rounded-b-lg p-4 justify-center align-middle border-t border-gray-200 bg-gray-50">
                            <Link viewTransition href={`/media/cdn/${media.id}`} download className="inline-flex items-center gap-2 text-white rounded-xl px-3 py-2 bg-blue-500 hover:bg-blue-600 text-sm">
                                <DownloadCloud size={16} />
                                Descargar
                            </Link>

                            <Link viewTransition href={index().url} className="inline-flex items-center gap-2 bg-locatel-oro text-white rounded-xl px-3 py-2 hover:bg-locatel-naranja text-sm">
                                <ArrowLeft size={14} />
                                Volver
                            </Link>
                        </div>
                    </div>

                    <div className="w-full md:w-2/3 flex items-start justify-center">
                        <div className="bg-white shadow rounded-lg p-4 w-full">
                            <div className="flex items-center justify-between mb-3">
                                <h3 className="text-lg font-semibold">Vista Previa</h3>
                            </div>

                            <div className="border border-dashed border-gray-200 rounded-lg p-4 grid place-items-center">
                                <div className="w-full max-h-[70vh] flex items-center justify-center">
                                    <MediaPreview
                                        media={media}
                                        cdnUrl={cdnUrl}
                                        imgRef={imgRef}
                                        videoRef={videoRef}
                                        dimensions={dimensions}
                                        loadError={loadError}
                                        onImageLoad={onImageLoad}
                                        onMediaError={onMediaError}
                                        handleRetry={handleRetry}
                                    />
                                </div>

                                <div className="w-full mt-3 text-xs text-gray-500 flex justify-between">
                                    <span>{dimensions ? `${dimensions.w} x ${dimensions.h} px` : media.mime_type?.startsWith('image/') && !loadError ? 'Cargando dimensiones...' : ''}</span>
                                    <span>{dimensions ? `Ratio ${ratioText(dimensions.w, dimensions.h)}` : ''}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
