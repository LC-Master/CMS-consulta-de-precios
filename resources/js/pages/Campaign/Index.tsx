import { useState } from 'react'
import { Head, router } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout';
import { useUpdateEffect } from '@/hooks/useUpdateEffect';
import { Filter } from '@/components/Filter';
import { Column } from '@/types/datatable.types';
import AnchorIcon from '@/components/ui/AnchorIcon';
import { Eye, Pencil, Trash } from 'lucide-react';
import { DataTable } from '@/components/DataTable';
import { Campaign, Props } from '@/types/campaign/index.types';
import useToast from '@/hooks/use-toast';
import { Button } from '@/components/ui/button';
import { Check, X } from 'lucide-react'
import { edit, index, show, activate } from '@/routes/campaign';
import useModal from '@/hooks/use-modal';
import { breadcrumbs } from '@/helpers/breadcrumbs';
import { PillStatus } from '@/components/ui/PillStatus';
import { StatusCampaignEnum } from '@/enums/statusCampaignEnum';
import DeleteCampaignModal from '@/components/modals/DeleteCampaignModal';
import FinishCampaignModal from '@/components/modals/FinishCampaignModal';

export default function CampaignsIndex({ campaigns, filters = {}, statuses = [], flash }: Props) {
    const [search, setSearch] = useState(filters.search || '')
    const { isOpen, closeModal, openModal } = useModal(false)
    const { isOpen: isOpenDelete, closeModal: closeDeleteModal, openModal: openModalDelete } = useModal(false)
    const [campaignId, setCampaignId] = useState<string | null>('');
    const [status, setStatus] = useState(filters.status || '')
    const { ToastContainer } = useToast(flash);

    const columns: Column<Campaign>[] = [
        {
            key: 'title',
            header: 'Título',
            render: (a) => a.title,
        },
        {
            key: 'status',
            header: 'Estado',
            render: (a) => (
                <PillStatus status={a.status.status} />
            ),
        },
        {
            key: 'start_at',
            header: 'Inicio',
            render: (a) => new Date(a.start_at).toLocaleDateString(),
        },
        {
            key: 'end_at',
            header: 'Fin',
            render: (a) => new Date(a.end_at).toLocaleDateString(),
        },
        {
            key: 'actions',
            header: 'Acciones',
            render: (a) => (
                <div className="flex gap-2">
                    {a.status.status === StatusCampaignEnum.DRAFT ? (
                        <Button title='Activar campaña' onClick={() => {
                            router.get(activate({ id: a.id }).url, {}, {
                                only: ['campaigns', 'flash'],
                                reset: ['campaigns', 'flash'],
                                preserveScroll: true
                            });
                        }} className='p-2 bg-locatel-claro h-8 text-white rounded-md'>
                            <Check className='w-4 h-4' />
                        </Button>
                    ) : (
                        a.status.status === StatusCampaignEnum.ACTIVE && (
                            <Button title='Finalizar campaña' onClick={() => {
                                setCampaignId(a.id);
                                openModal();
                            }} className='p-2 bg-red-600 h-8 text-white rounded-md'>
                                <X className='w-4 h-4' />
                            </Button>
                        )
                    )}
                    <AnchorIcon title="Ver campaña" href={show({ id: a.id }).url} icon={Eye} />
                    <AnchorIcon title="Editar campaña" className='p-2 bg-locatel-claro text-white rounded-md' href={edit({ id: a.id }).url} icon={Pencil} />
                    <Button title='Eliminar Campaña' onClick={() => {
                        setCampaignId(a.id);
                        openModalDelete();
                    }} className='p-2 bg-red-600 h-8 text-white rounded-md'>
                        <Trash className='w-4 h-4' />
                    </Button>
                </div>
            ),
        },
    ]
    useUpdateEffect(() => {
        router.get(
            window.location.pathname,
            { search, status },
            { preserveState: true, replace: true, preserveScroll: true }
        )
    }, [search, status])
    return (
        <AppLayout breadcrumbs={breadcrumbs('Lista de campañas', index().url)}>
            {ToastContainer()}
            <Head title="Lista de campañas" />
            <FinishCampaignModal isOpen={isOpen} campaignId={campaignId} closeModal={closeModal} setCampaignId={setCampaignId} />
            <DeleteCampaignModal isOpen={isOpenDelete} campaignId={campaignId} closeDeleteModal={closeDeleteModal} />
            <div className="space-y-4 px-4 pb-4">
                <Filter
                    filters={[
                        {
                            type: 'search',
                            key: 'search',
                            value: search,
                            label: 'Buscar por título',
                            placeholder: 'Buscar por título...',
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
                                ...statuses.map(s => ({
                                    value: s.id,
                                    label: s.status,
                                })),
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
                    data={campaigns.data}
                    columns={columns}
                    rowKey={(a) => a.id}
                    infiniteData="campaigns"
                />

            </div>
        </AppLayout>
    )
}