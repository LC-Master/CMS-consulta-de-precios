import { useState } from 'react'
import { router } from '@inertiajs/react'
import { Eye, Pencil } from 'lucide-react'
import AppLayout from '@/layouts/app-layout';
import { useUpdateEffect } from '@/hooks/useUpdateEffect';
import { Column, DataTable } from '@/components/DataTable';
import { Agreement, Props } from '@/types/agreement/index.types';
import { Filter } from '@/components/Filter';
import AnchorIcon from '@/components/ui/AnchorIcon';
import { breadcrumbs } from '../../tools/breadcrumbs'
import { index, show, edit } from '@/routes/agreement';

export default function AgreementsIndex({ agreements, filters = {}, statuses }: Props) {
    const [search, setSearch] = useState(filters.search || '')
    const [status, setStatus] = useState(filters.status || '')

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
                    <AnchorIcon href={show(a.id).url} icon={Eye} />

                    <AnchorIcon
                        href={edit(a.id).url}
                        icon={Pencil}
                    />
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
        <AppLayout breadcrumbs={breadcrumbs('Listado de Convenios', index().url)}>
            <div className="space-y-4 px-4 pb-4">
                <Filter
                    filters={[
                        {
                            type: 'search',
                            key: 'search',
                            value: search,
                            placeholder: 'Buscar por nombre o RIF...',
                            onChange: setSearch,
                        },
                        {
                            type: 'select',
                            key: 'status',
                            value: status,
                            placeholder: 'Filtrar por estado',
                            options: [
                                { value: '', label: 'Todos' },
                                ...statuses.map((s, index) => ({
                                    value: index,
                                    label: s,
                                })),
                            ],
                            onChange: setStatus,
                        },

                    ]}
                />

                <DataTable
                    data={agreements.data}
                    columns={columns}
                    rowKey={(a) => a.id}
                    infiniteData="agreements"
                />

            </div>
        </AppLayout>
    )
}