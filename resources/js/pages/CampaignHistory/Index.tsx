import { useState } from 'react'
import { router } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout';
import { useUpdateEffect } from '@/hooks/useUpdateEffect';
import { Filter } from '@/components/Filter';
import { Column } from '@/types/datatable.types';
import { DataTable } from '@/components/DataTable';
import { Campaign, Props } from '@/types/campaign/index.types';
import useToast from '@/hooks/use-toast';
import { destroy, index } from '@/routes/campaign';
import { show, clone, restore } from '@/routes/campaignsHistory';
import { breadcrumbs } from '@/helpers/breadcrumbs';
import { PillStatus } from '@/components/ui/PillStatus';
import { StatusCampaignEnum } from '@/enums/statusCampaignEnum';
import { Copy, Eye, RotateCcw, Trash } from 'lucide-react';
import { ActionMenu } from '@/components/ui/ActionMenu';

export default function CampaignsHistoryIndex({ campaigns, filters = {}, statuses, flash }: Props) {
    const [search, setSearch] = useState(filters.search || '')
    const [status, setStatus] = useState(filters.status || '')
    const [started_at, setStartedAt] = useState(filters.started_at || '')
    const [ended_at, setEndedAt] = useState(filters.ended_at || '')
    const { ToastContainer } = useToast(flash);

    const handlerResetFilters = () => {
        setSearch('');
        setStatus('');
        setStartedAt('');
        setEndedAt('');
    }

    const columns: Column<Campaign>[] = [
        {
            key: 'title',
            header: 'Título',
            render: (a) => a.title,
        },
        {
            key: 'status',
            header: 'Estado',
            render: (a) => {
                if (a.deleted_at) {
                    return <PillStatus status={StatusCampaignEnum.DELETED} />
                } else {
                    return <PillStatus status={a.status.status} />
                }
            }
            ,
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
                    <ActionMenu>
                        <ActionMenu.ItemLink href={show({ id: a.id }).url}>
                            <Eye className="w-4 h-4" />
                            <span>Ver</span>
                        </ActionMenu.ItemLink>

                        <ActionMenu.Item onClick={()=>{
                            router.post(clone({id:a.id}).url)
                        }}>
                            <Copy className="w-4 h-4" />
                            <span>Duplicar</span>
                        </ActionMenu.Item>

                        <ActionMenu.Separator />

                        {a.deleted_at ? (
                            <ActionMenu.Item onClick={() => {
                                router.post(restore({ id: a.id }).url, {
                                    data: { restore: true },
                                    only: ['campaigns', 'flash'],
                                    reset: ['campaigns', 'flash'],
                                    preserveScroll: true
                                });
                            }}>
                                <RotateCcw className="w-4 h-4 text-blue-500" />
                                <span className="text-blue-600 font-medium">Restaurar</span>
                            </ActionMenu.Item>
                        ) : (
                            <ActionMenu.Item variant="danger" onClick={() => {
                                router.delete(destroy({ id: a.id }).url, {
                                    only: ['campaigns', 'flash'],
                                    reset: ['campaigns', 'flash'],
                                    preserveScroll: true
                                });
                            }}>
                                <Trash className="w-4 h-4" />
                                <span>Eliminar</span>
                            </ActionMenu.Item>
                        )}
                    </ActionMenu>
                </div >
            ),
        },
    ]
    useUpdateEffect(() => {
        router.get(
            window.location.pathname,
            { search, status, ended_at, started_at },
            { preserveState: true, replace: true, preserveScroll: true }
        )
    }, [search, status, ended_at, started_at])
    return (
        <AppLayout breadcrumbs={breadcrumbs('Lista de campañas', index().url)}>
            {ToastContainer()}

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
                            type: 'date',
                            key: 'started_at',
                            label: 'Fecha de inicio',
                            value: started_at,
                            placeholder: 'Inicio',
                            onChange: (value) => { setStartedAt(value) },
                        },
                        {
                            type: 'date',
                            key: 'ended_at',
                            label: 'Fecha de fin',
                            value: ended_at,
                            placeholder: 'Fin',
                            onChange: (value) => { setEndedAt(value) },
                        },
                        {
                            type: 'select',
                            key: 'status',
                            label: 'Estado',
                            value: status,
                            placeholder: 'Filtrar por estado',
                            options: [
                                { value: '', label: 'Todos' },
                                { value: 'deleted', label: StatusCampaignEnum.DELETED },
                                ...statuses.map(s => ({
                                    value: s.id,
                                    label: s.status,
                                })),
                            ],
                            onChange: setStatus,
                        },
                        {
                            type: 'reset',
                            key: 'reset',
                            label: 'Reiniciar filtros',
                            onReset: handlerResetFilters,
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