import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/media';
import { BreadcrumbItem } from '@/types';
import { MediaItem } from '@/types/campaign/index.types';

export default function MediaShow({ media }: { media: MediaItem }) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Detalle de medio - ' + media.name,
            href: index().url,
        },
    ];
    console.log(media)
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="flex flex-col md:grid md:grid-cols-3 gap-6">
                <div className="md:col-span-2 flex flex-col mt-8 ml-2 shadow-2xl rounded-lg">
                    <div className="bg-white shadow rounded-lg p-6 flex-1 flex flex-col">
                        <div>
                            <Label className="font-bold">Nombre:</Label>
                            <p className="mb-4">{media.name}</p>

                            <Label className="font-bold">Tipo MIME:</Label>
                            <p className="mb-4">{media.mime_type}</p>

                            <Label className="font-bold">Tamaño:</Label>
                            <p className="mb-4">{media.size}</p>

                            <Label className="font-bold">Campañas asociadas:</Label>
                            {media.campaigns && media.campaigns.length > 0 ? (
                                <ul className="list-disc list-inside">
                                    {media.campaigns.map((campaign) => (
                                        <li key={campaign.id}>{campaign.title}</li>
                                    ))}
                                </ul>
                            ) : (
                                <p>No hay campañas asociadas a este medio.</p>
                            )}
                        </div>

                        <div className="mt-6 flex gap-3 shadow-2xs rounded-b-lg p-4 justify-end bg-gray-50">
                            <a href={`/media/cdn/${media.id}`} download className="inline-block  text-white rounded-xl p-2 bg-locatel-claro hover:bg-locatel-medio">
                                Descargar
                            </a>
                            <a href={index().url} className="inline-block bg-locatel-oro text-white rounded-xl p-2 hover:bg-locatel-naranja">
                                Volver
                            </a>
                        </div>
                    </div>
                </div>

                <div className="flex items-center justify-center">
                    <div className="bg-white shadow rounded-lg p-6 w-full  flex  items-center justify-center">
                        <div className="w-full">
                            {media.mime_type?.startsWith('image/') ? (
                                <img
                                    src={`/media/cdn/${media.id}`}
                                    alt={media.name}
                                    className="max-w-full max-h-[60vh] object-contain"
                                />
                            ) : media.mime_type?.startsWith('video/') ? (
                                <video controls className="max-w-full max-h-[60vh]">
                                    <source src={`/media/cdn/${media.id}`} type={media.mime_type} />
                                    Tu navegador no soporta la reproducción de videos.
                                </video>
                            ) : (
                                <div>
                                    <div className="animate-pulse bg-gray-100 rounded-lg p-6 flex flex-col items-center justify-center">
                                        <div className="w-24 h-24 bg-gray-200 rounded-md mb-4" />
                                        <div className="h-4 bg-gray-200 rounded w-3/4 mb-2" />
                                        <div className="h-4 bg-gray-200 rounded w-1/2" />
                                    </div>
                                    <p className="text-center text-sm text-gray-600 mt-3">
                                        Vista previa no disponible para este tipo de archivo.
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    )
}
