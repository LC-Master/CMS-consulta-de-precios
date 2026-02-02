import { DataTable } from "@/components/DataTable";
import AppLayout from "@/layouts/app-layout";
import { index } from "@/routes/centertokens";
import { breadcrumbs } from "@/helpers/breadcrumbs";
import { Column } from "@/types/datatable.types";
import { CenterToken, Props } from "@/types/centertokens/index.types";
import useToast from '@/hooks/use-toast';
import useModal from "@/hooks/use-modal";
import { useState } from "react";
import { Button } from "@/components/ui/button";
import DeleteCenterToken from "@/components/modals/DeleteCenterToken";
import { router } from "@inertiajs/react";
import { useUpdateEffect } from "@/hooks/useUpdateEffect";
import { Filter } from "@/components/Filter";
import { Plus } from "lucide-react";
import CreateCenterToken from "@/components/modals/CreateCenterToken";

export default function CenterTokensIndex({ stores, centerTokens, flash, filters }: Props) {
    const [tokenId, setTokenId] = useState<number | null>(null);
    const { isOpen, closeModal, openModal } = useModal(false);
    const { isOpen: isOpenCreateToken, closeModal: closeModalCreateToken, openModal: openModalCreateToken } = useModal(false);
    const { ToastContainer } = useToast(flash);
    const [selectedStore, setSelectedStore] = useState(filters.store || '');
    const [search, setSearch] = useState(filters.search || '');
    useUpdateEffect(() => {
        const timer = setTimeout(() => {
            router.get(
                index().url,
                { search: search, store: selectedStore },
                {
                    preserveState: true,
                    replace: true,
                    preserveScroll: true,
                    onSuccess: () => { router.reload({ only: ['centerTokens'], reset: ['centerTokens'] }); },
                }
            );
        }, 300);
        return () => clearTimeout(timer);
    }, [search, selectedStore]);
    const columns: Column<CenterToken>[] = [
        {
            key: 'name',
            header: 'Título',
            render: (ct) => ct.name,
        },
        {
            key: 'store',
            header: 'Centro',
            render: (ct) => ct.store?.name || 'N/A',
        },
        {
            key: 'last_used_at',
            header: 'Último uso',
            render: (ct) => ct.last_used_at ? new Date(ct.last_used_at).toLocaleString() : 'Nunca',
        },
        {
            key: 'created_at',
            header: 'Creado',
            render: (ct) => new Date(ct.created_at).toLocaleString(),
        },
        {
            key: 'actions',
            header: 'Acción',
            render: (ct) => (
                <div className="flex gap-2">
                    <Button
                        title='Eliminar token'
                        onClick={() => {
                            setTokenId(ct.id);
                            openModal();
                        }}
                        className='p-2 bg-red-600 h-8 hover:bg-red-700 text-white rounded-md'
                    >
                        Revocar
                    </Button>
                </div>
            ),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs('Lista de tokens', index().url)}>
            {ToastContainer()}
            {isOpen && <DeleteCenterToken closeModal={closeModal} tokenId={tokenId!} />}
            {isOpenCreateToken && <CreateCenterToken closeModal={closeModalCreateToken} stores={stores} />}
            <div className="space-y-4 px-4 pb-4">
                <div className="flex flex-wrap items-center gap-4">
                    <div className="flex-1 min-w-0">
                        <Filter
                            filters={[
                                {
                                    type: 'search',
                                    key: 'search',
                                    value: search,
                                    label: 'Buscar',
                                    placeholder: 'Buscar por título...',
                                    onChange: setSearch
                                },
                                {
                                    type: 'select',
                                    key: 'store_id',
                                    label: 'Tienda',
                                    value: selectedStore,
                                    placeholder: 'Filtrar por tienda',
                                    options: [
                                        { value: '', label: 'Todas las tiendas' },
                                        ...stores.map(s => ({
                                            value: s.id.toString(),
                                            label: `${s.name} (${s.store_code})`,
                                        })),
                                    ],
                                    onChange: setSelectedStore,
                                },
                            ]}
                        />
                    </div>

                    <Button type="button" onClick={openModalCreateToken}
                        className="flex-none flex items-center h-15 shadow-2xl gap-2 bg-locatel-medio hover:bg-locatel-claro text-white rounded-md px-4 mt-2 py-2"
                    >
                        <Plus className="w-4 h-4" />
                        <span className="whitespace-nowrap">Generar nuevo token</span>
                    </Button>
                </div>
                <DataTable
                    key={centerTokens.data.length}
                    infiniteData="centerTokens"
                    data={centerTokens.data}
                    rowKey={(a) => a.id}
                    columns={columns}
                />
            </div>
        </AppLayout>
    );
}
