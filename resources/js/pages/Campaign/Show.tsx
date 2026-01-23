import { PillStatus } from "@/components/ui/PillStatus";
import AppLayout from "@/layouts/app-layout";
import { index } from "@/routes/campaign";
import { breadcrumbs } from "@/helpers/breadcrumbs";
import { CampaignExtended } from "@/types/campaign/index.types";
import { Head, Link } from "@inertiajs/react";
import { Building, CalendarDays, CalendarX, Handshake, SquareLibrary, Store, Sun, Moon } from "lucide-react";
import { formatDate, isVideo } from "@/helpers/mediaTools";
import CenterCard from "@/components/CenterCard";
import ErrorBanner from "@/components/ui/ErrorBanner";

export default function CampaignShow({ campaign }: { campaign: CampaignExtended }) {
    const mediaAM = campaign.media
        .filter(m => m.pivot.slot === "am")
        .sort((a, b) => Number(a.pivot.position) - Number(b.pivot.position));

    const mediaPM = campaign.media
        .filter(m => m.pivot.slot === "pm")
        .sort((a, b) => Number(a.pivot.position) - Number(b.pivot.position));

    return (
        <AppLayout breadcrumbs={breadcrumbs('Detalles de campaña', index().url)}>
            <Head title="Detalles de campaña" />
            <div className="p-6 space-y-6">
                <div>
                    <div className="mb-6 ml-2">
                        <h1 className="text-3xl mb-2 font-bold">
                            Detalles de la campaña
                        </h1>
                        <p className="text-gray-600 text-sm">
                            Detalles para la campaña "<strong>{campaign.title}</strong>".
                        </p>
                    </div>

                    {/* Campaign info */}
                    <div className="flex flex-wrap md:flex-nowrap gap-4">
                        <div className="bg-white rounded-xl shadow shadow-stone-400 m-2 w-full md:w-[65%]">
                            <div className="flex items-center p-6 justify-between w-full 
                            border-b border-gray-300">
                                <h2 className="text-lg  font-semibold">Información general</h2>
                                <PillStatus status={campaign.status?.status} />
                            </div>
                            <div className="flex flex-row py-8 px-10 gap-6 mt-4 text-sm">
                                <div className="flex flex-col mr-6 space-y-6">
                                    <div>
                                        <p className="text-gray-500">Titulo de la campaña</p>
                                        <p className="font-medium">{campaign.title}</p>
                                    </div>

                                    <div>
                                        <p className="text-gray-500">Convenio</p>
                                        <div className="flex items-center gap-2">
                                            <Handshake className="w-4 h-4 text-gray-500" />
                                            <p className="font-medium">{campaign.agreement?.name}</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="flex flex-col mb-6 space-y-6">
                                    <div>
                                        <p className="text-gray-500">Departamento</p>
                                        <div className="flex items-center gap-2">
                                            <Building className="w-4 h-4 text-gray-500" />
                                            <p className="font-medium">{campaign.department?.name}</p>
                                        </div>
                                    </div>

                                    <div>
                                        <p className="text-gray-500">Creado</p>
                                        <p className="font-medium">{formatDate(campaign.created_at)}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Dates */}
                        <div className="bg-white rounded-xl flex flex-col text-sm shadow shadow-stone-400 m-2 w-full md:w-[35%]">
                            <div className="border-b py-6 px-7 border-gray-300 mb-4">
                                <h2 className="font-bold text-lg">Fechas</h2>
                            </div>

                            <div className="px-7 pb-6 space-y-6">
                                <div className="flex items-center pt-4 gap-4 justify-between">
                                    <div className="flex items-center gap-4">
                                        <div className="bg-gray-100 rounded-lg p-3 flex items-center justify-center" aria-hidden="true">
                                            <CalendarDays className="w-6 h-6 text-blue-500" />
                                        </div>
                                        <div>
                                            <p className="text-gray-500 text-xs">Inicio</p>
                                            <span className="font-semibold text-lg md:text-sm">{formatDate(campaign.start_at)}</span>
                                        </div>
                                    </div>
                                </div>

                                <div className="flex items-center gap-4 justify-between border-t pt-4">
                                    <div className="flex items-center gap-4">
                                        <div className="bg-gray-100 rounded-lg p-3 flex items-center justify-center" aria-hidden="true">
                                            <CalendarX className="w-6 h-6 text-red-500" />
                                        </div>
                                        <div>
                                            <p className="text-gray-500 text-xs">Fin</p>
                                            <p className="font-semibold text-lg md:text-sm">{formatDate(campaign.end_at)}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div className="bg-white rounded-xl shadow shadow-stone-400 m-2">
                        <div className="flex items-center justify-between border-b border-gray-300 p-6">
                            <div className="flex items-center gap-4">
                                <Store />
                                <h2 className="text-lg font-semibold">Centros asociados</h2>
                            </div>
                            <span className="text-sm text-gray-500 bg-gray-300 rounded-lg px-3 py-1">
                                {campaign.centers.length} centros
                            </span>
                        </div>
                        <div className="py-4 flex flex-row overflow-x-auto">
                            {campaign.centers ? campaign.centers.map(center => (
                                <CenterCard key={center.id} id={center.id} name={center.name} code={center.code} />
                            )) : (
                                <ErrorBanner message="No hay centros asociados" description="Esta campaña aún no tiene centros vinculados." />
                            )}
                        </div>
                    </div>
                    {/* Media AM */}
                    <div className="py-6 pl-2">

                        <div className="flex items-center mb-4 justify-between px-2">
                            <div className="flex items-center gap-2">
                                <Sun className="text-blue-400" />
                                <h2 className="text-lg font-semibold">Media AM</h2>
                            </div>
                            <span className="text-sm text-gray-500 rounded-lg px-3 py-1">
                                Total: {mediaAM.length}
                            </span>
                        </div>

                        {mediaAM.length === 0 ? (
                            <div className="bg-white rounded-xl p-6 shadow shadow-stone-400 m-2">
                                <div className="w-full flex flex-col items-center justify-center py-8 px-6 text-center space-y-3">
                                    <div className="relative">
                                        <div
                                            title="Sin contenido AM"
                                            aria-hidden="true"
                                            className="bg-linear-to-br from-blue-50 to-white text-blue-600 rounded-full p-4 inline-flex items-center justify-center shadow-md ring-1 ring-blue-100"
                                        >
                                            <SquareLibrary className="w-8 h-8" />
                                        </div>
                                        <div className="absolute -right-1 -bottom-1 bg-blue-600 text-white rounded-full p-1.5 flex items-center justify-center shadow text-xs ring-2 ring-white">
                                            <Sun className="w-3 h-3" />
                                        </div>
                                    </div>
                                    <h3 className="text-lg font-semibold">No hay contenido AM</h3>
                                </div>
                            </div>
                        ) : (
                            <div className="ml-2 flex flex-wrap gap-4">
                                {mediaAM.map(item => (
                                    <div
                                        key={item.id}
                                        className="flex-none w-1/2 md:w-1/4 border rounded-lg overflow-hidden"
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
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Media PM */}
                    <div className="py-6 pl-2">
                        <div className="flex items-center mb-4 justify-between px-2">
                            <div className="flex items-center gap-2">
                                <Moon className="text-blue-400" />
                                <h2 className="text-lg font-semibold">Media PM</h2>
                            </div>
                            <span className="text-sm text-gray-500 rounded-lg px-3 py-1">
                                Total: {mediaPM.length}
                            </span>
                        </div>

                        {mediaPM.length === 0 ? (
                            <div className="bg-white rounded-xl p-6 shadow shadow-stone-400 m-2">
                                <div className="w-full flex flex-col items-center justify-center py-8 px-6 text-center space-y-3">
                                    <div className="relative">
                                        <div
                                            title="Sin contenido PM"
                                            aria-hidden="true"
                                            className="bg-linear-to-br from-blue-50 to-white text-blue-600 rounded-full p-4 inline-flex items-center justify-center shadow-md ring-1 ring-blue-100"
                                        >
                                            <SquareLibrary className="w-8 h-8" />
                                        </div>
                                        <div className="absolute -right-1 -bottom-1 bg-blue-600 text-white rounded-full p-1.5 flex items-center justify-center shadow text-xs ring-2 ring-white">
                                            <Moon className="w-3 h-3" />
                                        </div>
                                    </div>
                                    <h3 className="text-lg font-semibold">No hay contenido PM</h3>
                                </div>
                            </div>
                        ) : (
                            <div className="ml-2 flex flex-wrap gap-4">
                                {mediaPM.map(item => (
                                    <div
                                        key={item.id}
                                        className="flex-none w-1/2 md:w-1/4 border rounded-lg overflow-hidden"
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
                                ))}
                            </div>
                        )}
                    </div>
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
                </div>
            </div>
        </AppLayout>
    );
}
