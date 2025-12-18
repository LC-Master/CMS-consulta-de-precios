import { useState } from 'react'
import { router, Link } from '@inertiajs/react' // 1. Importar Link
import { Pencil, Trash, Plus } from 'lucide-react' // 2. Importar Plus
import AppLayout from '@/layouts/app-layout';
import { useUpdateEffect } from '@/hooks/useUpdateEffect';
import { Column, DataTable } from '@/components/DataTable';
import { User, Props } from '@/types/user/index.types';
import { Filter } from '@/components/Filter';
import AnchorIcon from '@/components/ui/AnchorIcon';
import DeleteIcon from '@/components/ui/DeleteIcon';
import { BreadcrumbItem } from '@/types';

export default function UsersIndex({ users, filters = {} }: Props) {
    const [search, setSearch] = useState(filters.search || '')
    
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Lista de usuarios',
            href: '/user',
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
                        href={`/user/${u.id}/edit`}
                        icon={Pencil}
                    />
                    <DeleteIcon 
                         url={`/user/${u.id}`} 
                         icon={Trash} 
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
                
                {/* 3. Contenedor Flex para alinear Filtro y Botón */}
                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    
                    {/* El Filtro ocupa el espacio disponible */}
                    <div className="w-full sm:flex-1">
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
                    </div>

                    {/* Botón de Agregar Usuario */}
                    <Link
                        href="/user/create"
                        className="inline-flex items-center gap-2 px-4 py-2 bg-locatel-medio text-white rounded-md hover:brightness-95 transition-all shadow-sm font-medium text-sm whitespace-nowrap"
                    >
                        <Plus className="w-4 h-4" />
                        Agregar usuario
                    </Link>
                </div>

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