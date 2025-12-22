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

export default function UserEdit({ user, roles, statuses }: PropsEditPage) {
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

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        e.stopPropagation();
        put(update({ id: user.id }).url);
    };
    const optionsStatuses = statuses.map((status) => ({ value: status.value, label: status.name }))
    const optionsRoles = roles.map((role) => ({ value: role.name, label: role.name }))
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="flex flex-col items-center justify-center py-6 px-4 sm:px-6 lg:px-8">
                <div className="w-full max-w-2xl space-y-8 bg-white border-locatel-claro border-2 p-8 rounded-lg">

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
                            <Select
                                value={optionsRoles.find(option => option.value === data.role)}
                                onChange={(val) => setData('role', val ? String(val.value) : '')}
                                options={optionsRoles}
                                inputId="role"
                                name="role"
                                classNamePrefix="react-select"
                                placeholder="Selecciona un rol"
                                isClearable
                                aria-required={false}
                                aria-invalid={!!errors.role}
                                aria-describedby={errors.role ? 'role-error' : undefined}
                                styles={{
                                    control: (provided) => ({
                                        ...provided,
                                        borderColor: errors.role ? '#ef4444' : provided.borderColor,
                                        boxShadow: errors.role ? '0 0 0 1px rgba(239,68,68,0.25)' : provided.boxShadow,
                                        '&:hover': {
                                            borderColor: errors.role ? '#ef4444' : provided.borderColor,
                                        },
                                        borderRadius: '0.375rem',
                                    }),
                                }}
                            />
                            <InputError message={errors.role} />
                            {data.role && (
                                <div className="mt-2 p-3 bg-gray-50 rounded-md border border-gray-100 animate-in fade-in slide-in-from-top-1 duration-300">
                                    <p className="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">
                                        Permisos activos:
                                    </p>

                                    {roles.find(role => role.name === data.role) ? (
                                        <div className="flex flex-wrap gap-1.5">
                                            {roles.find(role => role.name === data.role)?.permissions.map((perm) => (
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
                                value={optionsStatuses.find(option => option.value === data.status)}
                                onChange={(val) => setData('status', val ? Number(val.value) : 1)}
                                options={optionsStatuses}
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