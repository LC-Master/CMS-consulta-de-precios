import { useState } from 'react'
import { router } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout';
import { useUpdateEffect } from '@/hooks/useUpdateEffect';
import { Filter } from '@/components/Filter';
import { Column } from '@/types/datatable.types';
import AnchorIcon from '@/components/ui/AnchorIcon';
import { Eye, Pencil } from 'lucide-react';
import { DataTable } from '@/components/DataTable';
import { Campaign, Props } from '@/types/campaign/index.types';
import useToast from '@/hooks/use-toast';
import { Button } from '@/components/ui/button';
import { Check, X } from 'lucide-react'
import { index } from '@/routes/campaign';
import Modal from '@/components/Modal';
import useModal from '@/hooks/use-modal';
import { breadcrumbs } from '@/helpers/breadcrumbs';

export default function CampaignsIndex({ campaigns, filters = {}, statuses = [], flash }: Props) {
    const [search, setSearch] = useState(filters.search || '')
    const { isOpen, closeModal, openModal } = useModal(false)
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
            render: (a) => a.status.status,
        },
        {
            key: 'created_at',
            header: 'Creada',
            render: (a) => new Date(a.created_at).toLocaleString(),
        },
        {
            key: 'actions',
            header: 'Acciones',
            render: (a) => (
                <div className="flex gap-2">
                    {a.status.status === 'Borrador' ? (
                        <Button title='Activar campaña' onClick={() => {
                            router.get(`/campaign/activate/${a.id}`, {}, {
                                onSuccess: () => {
                                    router.reload({ only: ['campaigns', 'flash'] });
                                },
                                preserveScroll: true
                            });
                        }} className='p-2 bg-locatel-claro h-8 text-white rounded-md'>
                            <Check className='w-4 h-4' />
                        </Button>
                    ) : (
                        a.status.status === 'Activa' && (
                            <Button title='Finalizar campaña' onClick={() => {
                                setCampaignId(a.id);
                                openModal();
                            }} className='p-2 bg-red-600 h-8 text-white rounded-md'>
                                <X className='w-4 h-4' />
                            </Button>
                        )
                    )}
                    <AnchorIcon title="Ver campaña" href={`/campaign/${a.id}`} icon={Eye} />
                    <AnchorIcon title="Editar campaña" className='p-2 bg-locatel-claro text-white rounded-md' href={`/campaign/${a.id}/edit`} icon={Pencil} />
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
            {isOpen && (<Modal className='w-90 bg-white p-6 ' closeModal={closeModal}>
                <h2 className="text-lg font-semibold mb-4">Confirmar finalización de campaña</h2>
                <p className="mb-6">¿Estás seguro de que deseas finalizar esta campaña? Esta acción no se puede deshacer.</p>
                <div className="flex justify-end w- gap-4">
                    <Button className='bg-locatel-oscuro text-white hover:bg-green-800' onClick={closeModal}>Cancelar</Button>
                    <Button
                        className="bg-red-600 text-white hover:bg-red-700"
                        onClick={() => {
                            router.visit(`/campaign/finish/${campaignId}`, {
                                method: 'get',
                                preserveState: false,
                                preserveScroll: true,
                                onSuccess: () => {
                                    closeModal();
                                    setCampaignId(null);
                                },
                            });
                        }}
                    >
                        Finalizar campaña
                    </Button>
                </div>
            </Modal>)}
            <div className="space-y-4 px-4 pb-4">
                <Filter
                    filters={[
                        {
                            type: 'search',
                            key: 'search',
                            value: search,
                            placeholder: 'Buscar por título...',
                            onChange: setSearch,
                        },
                        {
                            type: 'select',
                            key: 'status',
                            value: status,
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
                    ]}
                />

                <DataTable
                    key={JSON.stringify(campaigns.data.map(c => c.id + c.status.status))}
                    data={campaigns.data}
                    columns={columns}
                    rowKey={(a) => a.id}
                    infiniteData="campaigns"
                />

            </div>
        </AppLayout>
    )
}