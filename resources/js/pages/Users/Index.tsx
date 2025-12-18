import { useState } from 'react'
import { router } from '@inertiajs/react'
import { Pencil, Trash2 } from 'lucide-react' // Asumiendo Trash2
import AppLayout from '@/layouts/app-layout';
import { useUpdateEffect } from '@/hooks/useUpdateEffect';
import { Column, DataTable } from '@/components/DataTable';
import { User, Props } from '@/types/user/index.types';
import { Filter } from '@/components/Filter';
import AnchorIcon from '@/components/ui/AnchorIcon';
import DeleteIcon from '@/components/ui/DeleteIcon'; // Si tienes un componente para borrar
import { BreadcrumbItem } from '@/types';

export default function UsersIndex({ users, filters = {} }: Props) {
    const [search, setSearch] = useState(filters.search || '')
    
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Lista de usuarios',
            href: '/users',
        },
    ];

    const columns: Column<User>[] = [
        {
            key: 'name',
            header: 'Nombre',
            render: (u) => u.name,
        },
        {
            key: 'email',
            header: 'Correo Electrónico',
            render: (u) => u.email,
        },
        {
            key: 'created_at',
            header: 'Fecha Registro',
            render: (u) => new Date(u.created_at).toLocaleDateString(),
        },
        {
            key: 'actions',
            header: 'Acciones',
            render: (u) => (
                <div className="flex gap-2">
                    <AnchorIcon
                        url={`/users/${u.id}/edit`}
                        icon={Pencil}
                    />
                    {/* Asumiendo que tienes un DeleteIcon o lógica similar */}
                    <DeleteIcon 
                         url={`/users/${u.id}`} 
                         icon={Trash2} 
                    />
                </div>
            ),
        },
    ]

    useUpdateEffect(() => {
        router.get(
            window.location.pathname,
            { search },
            { preserveState: true, replace: true, preserveScroll: true }
        )
    }, [search])

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="space-y-4 px-4 pb-4">
                <Filter
                    filters={[
                        {
                            type: 'search',
                            key: 'search',
                            value: search,
                            placeholder: 'Buscar por nombre o email...',
                            onChange: setSearch,
                        },
                    ]}
                />

                <DataTable
                    data={users.data}
                    columns={columns}
                    rowKey={(u) => u.id}
                    infiniteData="users"
                />
            </div>
        </AppLayout>
    )
}