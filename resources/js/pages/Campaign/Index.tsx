import { useState } from 'react'
import { router } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout';
import { useUpdateEffect } from '@/hooks/useUpdateEffect';
import { Filter } from '@/components/Filter';
import { Column } from '@/types/datatable.types';
import AnchorIcon from '@/components/ui/AnchorIcon';
import { Eye } from 'lucide-react';
import { DataTable } from '@/components/DataTable';
import { Campaign, Props } from '@/types/campaign/index.types';
import { BreadcrumbItem } from '@/types';
import { index } from '@/routes/campaign';
import useToast from '@/hooks/use-toast';

export default function CampaignsIndex({ campaigns, filters = {}, statuses = [], flash }: Props) {
    const [search, setSearch] = useState(filters.search || '')
    const [status, setStatus] = useState(filters.status || '')
    const { ToastContainer } = useToast(flash);
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Lista de campañas',
            href: index().url,
        },
    ];
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
            render: (a) => a.created_at,
        },
        {
            key: 'actions',
            header: 'Acciones',
            render: (a) => (
                <div className="flex gap-2">
                    <AnchorIcon url={`/campaign/${a.id}`} icon={Eye} />
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
        <AppLayout breadcrumbs={breadcrumbs}>
            {ToastContainer()}

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
                    data={campaigns.data}
                    columns={columns}
                    rowKey={(a) => a.id}
                    infiniteData="campaigns"
                />

            </div>
        </AppLayout>
    )
}