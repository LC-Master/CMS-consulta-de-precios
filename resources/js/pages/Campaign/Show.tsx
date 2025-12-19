import AppLayout from "@/layouts/app-layout";
import { index } from "@/routes/campaign";
import { BreadcrumbItem } from "@/types";
import { CampaignExtended } from "@/types/campaign/index.types";
import { Link } from "@inertiajs/react";
function formatDate(date?: string) {
    if (!date) return "-";
    return new Date(date).toLocaleString();
}

function isVideo(mime_type: string) {
    return mime_type.startsWith("video");
}

export default function CampaignShow({ campaign }: { campaign: CampaignExtended }) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Detalle de campaña',
            href: index().url,
        },
    ];
    const mediaAM = campaign.media
        .filter(m => m.pivot.slot === "am")
        .sort((a, b) => Number(a.pivot.position) - Number(b.pivot.position));

    const mediaPM = campaign.media
        .filter(m => m.pivot.slot === "pm")
        .sort((a, b) => Number(a.pivot.position) - Number(b.pivot.position));

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="max-w-7xl mx-auto mt-2">

                {/* Page header */}
                <div className="flex items-center justify-between px-2">
                    <Link
                        viewTransition
                        href={index().url}
                        className="inline-flex items-center gap-2 bg-locatel-claro hover:bg-locatel-medio px-4 py-2 rounded-md text-white text-sm font-medium shadow"
                    >
                        ← Volver
                    </Link>

                    <span className="text-sm text-gray-500">
                        ID: {campaign.id}
                    </span>
                </div>

                {/* Campaign info */}
                <div className="bg-white rounded-xl p-6 shadow shadow-stone-400 m-2">
                    <h1 className="text-2xl font-semibold">{campaign.title}</h1>

                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 text-sm">
                        <div>
                            <p className="text-gray-500">Estado</p>
                            <p className="font-medium">{campaign.status?.status}</p>
                        </div>

                        <div>
                            <p className="text-gray-500">Agreement</p>
                            <p className="font-medium">{campaign.agreement?.name}</p>
                        </div>

                        <div>
                            <p className="text-gray-500">Departamento</p>
                            <p className="font-medium">{campaign.department?.name}</p>
                        </div>

                        <div>
                            <p className="text-gray-500">Creado</p>
                            <p className="font-medium">{formatDate(campaign.created_at)}</p>
                        </div>
                    </div>
                </div>

                {/* Dates */}
                <div className="bg-white rounded-xl p-6 grid grid-cols-2 gap-4 text-sm shadow shadow-stone-400 m-2">
                    <div>
                        <p className="text-gray-500">Inicio</p>
                        <p className="font-medium">{formatDate(campaign.start_at)}</p>
                    </div>

                    <div>
                        <p className="text-gray-500">Fin</p>
                        <p className="font-medium">{formatDate(campaign.end_at)}</p>
                    </div>
                </div>

                {/* Media AM */}
                <div className="bg-white rounded-xl p-6 shadow shadow-stone-400 m-2">
                    <h2 className="text-lg font-semibold mb-4">Media AM</h2>

                    {mediaAM.length === 0 ? (
                        <p className="text-gray-500 text-sm">No hay contenido AM</p>
                    ) : (
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                            {mediaAM.map(item => {
                                console.log(item);
                                return (
                                    <div
                                        key={item.id}
                                        className="border rounded-lg overflow-hidden"
                                    >
                                        {isVideo(item.mime_type) ? (
                                            <video
                                                src={`/media/cdn/${item.id}`}
                                                controls
                                                className="aspect-w-16 aspect-h-9 w-full h-40 object-cover"
                                            />
                                        ) : (
                                            <img
                                                src={`/media/cdn/${item.id}`}
                                                className="aspect-w-16 aspect-h-9 w-full h-40 object-cover"
                                            />
                                        )}

                                        <div className="p-2 text-xs space-y-1">
                                            <p className="font-medium truncate">{item.name}</p>
                                            <p className="text-gray-500">
                                                Posición: {item.pivot.position}
                                            </p>
                                            {item.duration_seconds && (
                                                <p className="text-gray-500">
                                                    Duración: {item.duration_seconds}s
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                )
                            })}
                        </div>
                    )}
                </div>

                {/* Media PM */}
                <div className="bg-white rounded-xl p-6 shadow shadow-stone-400 m-2">
                    <h2 className="text-lg font-semibold mb-4">Media PM</h2>

                    {mediaPM.length === 0 ? (
                        <p className="text-gray-500 text-sm">No hay contenido PM</p>
                    ) : (
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                            {mediaPM.map(item => (
                                <div
                                    key={item.id}
                                    className="border rounded-lg overflow-hidden"
                                >
                                    {isVideo(item.mime_type) ? (
                                        <video
                                            src={`/media/cdn/${item.id}`}
                                            controls
                                            className="w-full h-40 object-cover"
                                        />
                                    ) : (
                                        <img
                                            src={`/media/cdn/${item.id}`}
                                            className="w-full h-40 object-cover"
                                        />
                                    )}

                                    <div className="p-2 text-xs space-y-1">
                                        <p className="font-medium truncate">{item.name}</p>
                                        <p className="text-gray-500">
                                            Posición: {item.pivot.position}
                                        </p>
                                        {item.duration_seconds && (
                                            <p className="text-gray-500">
                                                Duración: {item.duration_seconds}s
                                            </p>
                                        )}
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>

            </div>
        </AppLayout>
    );
}
