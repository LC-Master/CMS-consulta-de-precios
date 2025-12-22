import { useMemo } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { useForm } from '@inertiajs/react';
import { PropsEditPage } from '@/types/user/index.types';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { update } from '@/routes/user';
import Select from 'react-select'

export default function UserEdit({ user, roles }: PropsEditPage) {

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Editar usuario',
            href: `/user/${user.id}/edit`,
        },
    ];

    const { data, setData, processing, errors, put } = useForm({
        name: user.name,
        email: user.email,
        status: user.status ?? 1,
        role: user.roles && user.roles.length > 0 ? user.roles[0].name : '',
        password: '',
        password_confirmation: '',
    });

    const selectedRoleInfo = useMemo(() => {
        return roles.find(r => r.name === data.role);
    }, [data.role, roles]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        e.stopPropagation();
        put(update({ id: user.id }).url);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="flex flex-col items-center justify-center py-6 px-4 sm:px-6 lg:px-8">
                <div className="w-full max-w-2xl space-y-8 bg-white p-8 shadow rounded-lg">

                    <div className="text-center">
                        <h2 className="text-2xl font-bold tracking-tight text-gray-900">
                            Editar usuario
                        </h2>
                        <p className="mt-2 text-sm text-gray-600">
                            Actualiza la información y permisos del usuario
                        </p>
                    </div>

                    <form onSubmit={handleSubmit} className="mt-8 space-y-6">
                        <div className="grid gap-2">
                            <Label htmlFor="name">Nombre</Label>
                            <Input
                                id="name"
                                type="text"
                                value={data.name}
                                required
                                placeholder="Nombre completo"
                                onChange={(e) => setData('name', e.target.value)}
                            />
                            <InputError message={errors.name} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="role">Rol de Usuario</Label>
                            <select
                                id="role"
                                value={data.role}
                                required
                                onChange={(e) => setData('role', e.target.value)}
                                className={`flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 
                                    `}
                            >
                                <option value="" disabled>Selecciona un rol</option>
                                {roles.map((role) => (
                                    <option key={role.id} value={role.name}>
                                        {role.name}
                                    </option>
                                ))}
                            </select>

                            <InputError message={errors.role} />
                            {selectedRoleInfo && (
                                <div className="mt-2 p-3 bg-gray-50 rounded-md border border-gray-100 animate-in fade-in slide-in-from-top-1 duration-300">
                                    <p className="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">
                                        Permisos activos:
                                    </p>

                                    {selectedRoleInfo.permissions.length > 0 ? (
                                        <div className="flex flex-wrap gap-1.5">
                                            {selectedRoleInfo.permissions.map((perm) => (
                                                <span
                                                    key={perm.id}
                                                    className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200"
                                                >
                                                    {perm.name}
                                                </span>
                                            ))}
                                        </div>
                                    ) : (
                                        <p className="text-xs text-gray-400 italic">Este rol no tiene permisos asignados.</p>
                                    )}
                                </div>
                            )}
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="status">Estatus</Label>
                            <Select
                                id='status'
                                value={data.status === 1 ? { value: 1, label: 'Activo' } : { value: 0, label: 'Inactivo' }}
                                onChange={(selectedOption) => {
                                    if (selectedOption) {
                                        setData('status', selectedOption.value);
                                    }
                                }}
                                options={[
                                    { value: 1, label: 'Activo' },
                                    { value: 0, label: 'Inactivo' },
                                ]}
                            />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="email">Correo electrónico</Label>
                            <Input
                                id="email"
                                type="email"
                                value={data.email}
                                required
                                placeholder="email@ejemplo.com"
                                onChange={(e) => setData('email', e.target.value)}
                            />
                            <InputError message={errors.email} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="password">
                                Contraseña
                            </Label>
                            <Input
                                id="password"
                                type="password"
                                value={data.password}
                                placeholder="********"
                                autoComplete="new-password"
                                onChange={(e) => setData('password', e.target.value)}
                            />
                            <InputError message={errors.password} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="password_confirmation">Confirmar Contraseña</Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                value={data.password_confirmation}
                                placeholder="********"
                                autoComplete="new-password"
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                            />
                        </div>

                        <div className="flex flex-col gap-3 pt-4">
                            <Button
                                type="submit"
                                className="w-full bg-locatel-oscuro text-white hover:bg-locatel-oscuro cursor-pointer"
                                disabled={processing}
                            >
                                {processing && <Spinner className="mr-2 h-4 w-4" />}
                                Actualizar Usuario
                            </Button>

                            <Button
                                type="button"
                                className="w-full bg-red-600 text-white hover:bg-red-700 cursor-pointer"
                                onClick={() => (window.location.href = '/user')}
                            >
                                Cancelar
                            </Button>
                        </div>

                    </form>
                </div>
            </div>
        </AppLayout>
    );
}