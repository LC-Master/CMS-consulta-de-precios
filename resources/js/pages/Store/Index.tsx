import { ActionMenu } from "@/components/ui/ActionMenu";
import { Column } from "@/types/datatable.types";
import { Store } from "@/types/store/index.type";
import { Button } from "@headlessui/react";
import { Head, router } from "@inertiajs/react";
import { Eye, Pencil, FileSpreadsheet, AlertCircle } from "lucide-react";
import { index } from "@/routes/stores";
import { DataTable } from "@/components/DataTable";
import { breadcrumbs } from "@/helpers/breadcrumbs";
import useToast from "@/hooks/use-toast";
import AppLayout from "@/layouts/app-layout";
import { Filter } from "@/components/Filter";
import { useEffect, useState } from "react";
import { useUpdateEffect } from "@/hooks/useUpdateEffect";
import { Props } from "@/types/store/page.type";
import useModal from "@/hooks/use-modal";
import PlaceHolderMangerModal from "@/components/modals/PlaceHolderMangerModal";
import StoreDetailsModal from "@/components/modals/StoreDetailsModal";
import MediaErrorsModal from "@/components/modals/MediaErrorsModal";
import SyncStatusPill from "@/components/SyncStatusPill";
import { useEcho } from "@laravel/echo-react";
import { SYNC_STATUS_TRANSLATIONS } from "@/i18n/sync-status";

export default function StoreIndex({ stores, filters = {}, flash }: Props) {
    const { ToastContainer } = useToast(flash);
    const [search, setSearch] = useState(filters.search || '')
    const [status, setStatus] = useState(filters.status || '')
    const { listen, stopListening } = useEcho('monitoring', '.sync.updated', () => router
        .get(window.location.pathname, {}, { preserveState: true, replace: true, preserveScroll: true, fresh: true }))
    console.log(stores)
    useEffect(() => {
        listen()
        return () => {
            stopListening()
        }
    }, [listen, stopListening])

    const { closeModal, isOpen, openModal } = useModal(false)

    const { closeModal: closeDetails, isOpen: isDetailsOpen, openModal: openDetails } = useModal(false)

    const { closeModal: closeErrors, isOpen: isErrorsOpen, openModal: openErrors } = useModal(false)

    const [storeSelected, setStoreSelected] = useState<Store | null>(null);

    useUpdateEffect(() => {
        router.get(
            window.location.pathname,
            { search, status },
            { preserveState: true, replace: true, preserveScroll: true }
        )
    }, [search, status])

    const columns: Column<Store>[] = [
        {
            key: 'name',
            header: 'Nombre',
            render: (a) => a.name,
        },
        {
            key: 'store_code',
            header: 'Código de tienda',
            render: (a) => a.store_code,
        },
        {
            key: 'region',
            header: 'Región',
            render: (a) => a.region,
        },
        {
            key: 'state_sync',
            header: 'Estado de sincronización',
            render: (a) => {
                const syncState = a.sync_state;
                if (!syncState) return <span className="text-xs text-gray-400 italic">No configurado</span>;

                return (
                    <div className="flex flex-col items-start gap-1">
                        <SyncStatusPill status={syncState.sync_status} />
                        {syncState.last_synced_at && (
                            <span className="text-[10px] text-gray-500 font-medium">
                                {new Date(syncState.last_synced_at).toLocaleString('es-VE', {
                                    day: 'numeric',
                                    month: 'numeric',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                })}
                            </span>
                        )}
                    </div>
                );
            },
        },
        {
            key: 'media_errors',
            header: 'Media con errores',
            render: (a) => {
                const errorCount = a.center_media_errors?.length || 0;
                if (errorCount === 0) return <span className="text-gray-400 text-xs">Sin errores</span>;

                return (
                    <button
                        onClick={() => {
                            setStoreSelected(a);
                            openErrors();
                        }}
                        className="flex items-center gap-1.5 px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-600 rounded-full transition-colors text-xs font-bold border border-red-100 group shadow-sm"
                    >
                        <AlertCircle className="w-3.5 h-3.5" />
                        <span>{errorCount} {errorCount === 1 ? 'Error' : 'Errores'}</span>
                    </button>
                );
            }
        },
        {
            key: 'actions',
            header: 'Acciones',
            render: (a) => {
                const hasUrl = !!a.sync_state?.url;

                return (
                    <div className="flex gap-2">
                        <Button
                            disabled={!hasUrl}
                            onClick={() => router.post(`/stores/${a.id}/force-sync`)}
                            className={`px-3 h-8 text-xs font-medium text-white rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 ${hasUrl
                                ? 'bg-locatel-naranja hover:bg-orange-500 focus:ring-locatel-naranja'
                                : 'bg-gray-300 cursor-not-allowed opacity-70'
                                }`}
                        >
                            Forzar sincronización
                        </Button>
                        <Button
                            disabled={!hasUrl}
                            onClick={() => router.post(`/stores/${a.id}/force-token`)}
                            className={`px-3 h-8 text-xs font-medium text-white rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 ${hasUrl
                                ? 'bg-locatel-medio hover:bg-locatel-claro focus:ring-locatel-claro'
                                : 'bg-gray-300 cursor-not-allowed opacity-70'
                                }`}
                        >
                            Forzar Token
                        </Button>
                        <ActionMenu>
                            <ActionMenu.Item onClick={() => {
                                setStoreSelected(a);
                                openModal();
                            }}>
                                <Eye className="w-4 h-4" />
                                <span>Imagen de emergencia</span>
                            </ActionMenu.Item>

                            <ActionMenu.Item onClick={() => {
                                setStoreSelected(a);
                                openErrors();
                            }}>
                                <Pencil className="w-4 h-4" />
                                <span>Lista de medios con errores</span>
                            </ActionMenu.Item>

                            <ActionMenu.Separator />

                            <ActionMenu.Item onClick={() => {
                                setStoreSelected(a);
                                openDetails();
                            }}>
                                <FileSpreadsheet className="w-4 h-4" />
                                <span>Detalles de la tienda</span>
                            </ActionMenu.Item>
                        </ActionMenu>
                    </div>
                )
            },
        },
    ]

    return (
        <AppLayout breadcrumbs={breadcrumbs('Lista de Tiendas', index().url)}>
            {ToastContainer()}
            <PlaceHolderMangerModal isOpen={isOpen} store={storeSelected} onClose={closeModal} />
            <StoreDetailsModal isOpen={isDetailsOpen} store={storeSelected} onClose={closeDetails} />
            <MediaErrorsModal isOpen={isErrorsOpen} errors={storeSelected?.center_media_errors} onClose={closeErrors} storeName={storeSelected?.name} />
            <Head title="Lista de Tiendas" />
            <div className="space-y-4 px-4 pb-4">
                <Filter
                    filters={[
                        {
                            type: 'search',
                            key: 'search',
                            value: search,
                            label: 'Buscar por nombre',
                            placeholder: 'Buscar por nombre...',
                            onChange: setSearch,
                        },
                        {
                            type: 'select',
                            key: 'status',
                            value: status,
                            label: 'Estatus',
                            placeholder: 'Filtrar por estado',
                            options: [
                                { value: '', label: 'Todos' },
                                { value: 'pending', label: SYNC_STATUS_TRANSLATIONS['pending'] },
                                { value: 'syncing', label: SYNC_STATUS_TRANSLATIONS['syncing'] },
                                { value: 'success', label: SYNC_STATUS_TRANSLATIONS['success'] },
                                { value: 'failed', label: SYNC_STATUS_TRANSLATIONS['failed'] },
                                { value: 'stale', label: SYNC_STATUS_TRANSLATIONS['stale'] },
                            ],
                            onChange: setStatus,
                        },
                        {
                            type: 'reset',
                            onReset: () => {
                                setSearch('');
                                setStatus('');
                            },
                            key: 'reset',
                        }
                    ]}
                />
                <DataTable
                    data={stores.data}
                    columns={columns}
                    rowKey={(a) => a.id}
                    infiniteData="stores"
                />

            </div>
        </AppLayout>
    );
}