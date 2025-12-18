import AppLayout from '@/layouts/app-layout'
import { BreadcrumbItem } from '@/types'
import { useForm } from '@inertiajs/react'
import { User } from '@/types/user/index.types'

interface Props {
    user: User;
}

export default function UserEdit({ user }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Usuarios',
            href: '/user', 
        },
        {
            title: 'Editar usuario',
            href: `/user/${user.id}/edit`, 
        },
    ];

    const { data, setData, processing, errors, put } = useForm({
        name: user.name,
        email: user.email,
        password: '',
        password_confirmation: '',
    })

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault()
        put(`/user/${user.id}`) 
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="p-6 space-y-6">
                <form id="form" onSubmit={handleSubmit} className="space-y-4">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {/* Nombre */}
                        <div>
                            <label htmlFor="name" className="block text-sm font-medium text-gray-700">Nombre</label>
                            <input
                                type="text"
                                id="name"
                                value={data.name}
                                required
                                onChange={e => setData('name', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.name && <p className="text-red-500 text-sm mt-1">{errors.name}</p>}
                        </div>

                        {/* Email */}
                        <div>
                            <label htmlFor="email" className="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input
                                type="email"
                                id="email"
                                value={data.email}
                                required
                                onChange={e => setData('email', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.email && <p className="text-red-500 text-sm mt-1">{errors.email}</p>}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {/* Contraseña (Opcional) */}
                        <div>
                            <label htmlFor="password" className="block text-sm font-medium text-gray-700">
                                Contraseña <span className="text-gray-400 font-normal">(Dejar en blanco para mantener)</span>
                            </label>
                            <input
                                type="password"
                                id="password"
                                value={data.password}
                                placeholder='********'
                                onChange={e => setData('password', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.password && <p className="text-red-500 text-sm mt-1">{errors.password}</p>}
                        </div>

                        {/* Confirmar Contraseña */}
                        <div>
                            <label htmlFor="password_confirmation" className="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                value={data.password_confirmation}
                                placeholder='********'
                                onChange={e => setData('password_confirmation', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                        </div>
                    </div>
                </form>

                <div className="flex flex-wrap justify-center gap-3">
                    <button
                        form="form"
                        type="submit"
                        className="bg-locatel-medio text-white rounded-md px-6 py-3 shadow hover:brightness-95 disabled:opacity-50"
                        disabled={processing}
                    >
                        Actualizar
                    </button>

                    <button
                        type="button"
                        onClick={() => (window.location.href = '/user')}
                        className="bg-red-500 text-white rounded-md px-6 py-3 shadow hover:brightness-95"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </AppLayout>
    )
}