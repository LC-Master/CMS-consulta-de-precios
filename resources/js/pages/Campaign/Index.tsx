import { useState } from 'react'
import { InfiniteScroll, router } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout';
import { useUpdateEffect } from '@/hooks/useUpdateEffect';
import { Props } from '@/types/campaign/index.types';
import { Filter } from '@/components/Filter';



export default function CampaignsIndex({ campaigns, filters = {}, statuses = [] }: Props) {
    const [search, setSearch] = useState(filters.search || '')
    const [status, setStatus] = useState(filters.status || '')
    useUpdateEffect(() => {
        router.get(
            window.location.pathname,
            { search, status },
            { preserveState: true, replace: true, preserveScroll: true }
        )
    }, [search, status])
    const columns: Column<Agreement>[] = [
        {
            key: 'name',
            header: 'Nombre',
            render: (a) => a.name,
        },
        {
            key: 'legal_name',
            header: 'Nombre Legal',
            render: (a) => a.legal_name,
        },
        {
            key: 'tax_id',
            header: 'RIF',
            render: (a) => a.tax_id,
        }, {
            key: 'contact_phone',
            header: 'Teléfono',
            render: (a) => a.contact_phone,
        }, {
            key: 'contact_person',
            header: 'Persona Contacto',
            render: (a) => a.contact_person,
        },
        {
            key: 'status',
            header: 'Estatus',
            render: (a) => (
                <span
                    className={`px-2 inline-flex text-xs font-semibold rounded-full ${Number(a.is_active)
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                        }`}
                >
                    {Number(a.is_active) ? 'Activo' : 'Inactivo'}
                </span>
            ),
        },
        {
            key: 'actions',
            header: 'Acciones',
            render: (a) => (
                <div className="flex gap-2">
                    <a
                        href={`/agreement/${a.id}`}
                        className="p-2 bg-locatel-medio text-white rounded-md"
                    >
                        <Eye className="w-4 h-4" />
                    </a>

                    <a
                        href={`/agreement/${a.id}/edit`}
                        className="p-2 bg-locatel-oscuro text-white rounded-md"
                    >
                        <Pencil className="w-4 h-4" />
                    </a>
                </div>
            ),
        },
    ]
    return (
        <AppLayout>
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

            </div>
        </AppLayout>
    )
}