import { useState } from 'react'
import { router, Link } from '@inertiajs/react'
import { Pencil, Plus, Trash2, RefreshCw } from 'lucide-react'
import AppLayout from '@/layouts/app-layout'
import { useUpdateEffect } from '@/hooks/useUpdateEffect'
import { Column, DataTable } from '@/components/DataTable'
import { User, Props } from '@/types/user/index.types'
import { Filter } from '@/components/Filter'
import AnchorIcon from '@/components/ui/AnchorIcon'
import { index } from '@/routes/user'
import { breadcrumbs } from '@/helpers/breadcrumbs'
import useModal from '@/hooks/use-modal'
import UserActionModal from '@/components/modals/UserActionModal'
import useToast from '@/hooks/use-toast'

export default function UsersIndex({ users, filters = {}, flash }: Props) { 
    const [search, setSearch] = useState(filters.search || '')
    
    const { ToastContainer } = useToast(flash);

    const { isOpen, closeModal, openModal } = useModal(false)
    const [selectedUserId, setSelectedUserId] = useState<string>('')
    const [actionType, setActionType] = useState<'delete' | 'restore' | null>(null)

    const confirmDelete = (id: string) => {
        setSelectedUserId(id);
        setActionType('delete');
        openModal();
    }

    const confirmRestore = (id: string) => {
        setSelectedUserId(id);
        setActionType('restore');
        openModal();
    }

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
            key: 'status',
            header: 'Estatus',
            render: (u) => (
                <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                    !u.deleted_at 
                    ? 'bg-green-100 text-green-800 border border-green-200' 
                    : 'bg-red-100 text-red-800 border border-red-200'
                }`}>
                    {!u.deleted_at ? 'Activo' : 'Inactivo'}
                </span>
            ),
        },
        {
            key: 'created_at',
            header: 'Fecha Registro',
            render: (u) => new Date(u.created_at).toLocaleString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            }),
        },
        {
            key: 'actions',
            header: 'Acciones',
            render: (u) => (
                <div className="flex gap-2">
                    {!u.deleted_at && (
                        <AnchorIcon
                            href={`/user/${u.id}/edit`}
                            icon={Pencil}
                            title="Editar usuario"
                        />
                    )}

                    {!u.deleted_at ? (
                        <button
                            onClick={() => confirmDelete(String(u.id))}
                            className="p-2 bg-red-500 rounded-md text-white hover:bg-red-600 transition-colors shadow-sm cursor-pointer"
                            title="Desactivar usuario"
                        >
                            <Trash2 className="w-4 h-4" />
                        </button>
                    ) : (
                        <button
                            onClick={() => confirmRestore(String(u.id))}
                            className="p-2 bg-orange-500 rounded-md text-white hover:bg-orange-600 transition-colors shadow-sm cursor-pointer"
                            title="Restaurar usuario"
                        >
                            <RefreshCw className="w-4 h-4" />
                        </button>
                    )}
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
        <AppLayout breadcrumbs={breadcrumbs('Lista de usuarios', index().url)}>
            
            {ToastContainer()}

            <UserActionModal 
                isOpen={isOpen}
                closeModal={closeModal}
                userId={selectedUserId}
                actionType={actionType}
                setUserId={setSelectedUserId}
            />

            <div className="space-y-4 px-4 pb-4">
                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div className="w-full sm:flex-1">
                        <Filter
                            filters={[
                                {
                                    type: 'search',
                                    key: 'search',
                                    value: search,
                                    label: 'Buscar por nombre o email',
                                    placeholder: 'Buscar por nombre o email...',
                                    onChange: setSearch,
                                },
                            ]}
                        />
                    </div>

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