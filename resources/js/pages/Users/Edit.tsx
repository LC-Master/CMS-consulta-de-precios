import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { PropsEditPage } from '@/types/user/index.types';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { update, index, edit } from '@/routes/user';
import Select from 'react-select';
import { breadcrumbs } from '@/helpers/breadcrumbs';

export default function UserEdit({ user, roles, statuses }: PropsEditPage) {
    const optionsRoles = roles.map((role) => ({ value: role.name, label: role.name }));
    const optionsStatuses = statuses.map((status) => ({ value: status.value, label: status.name }));

    const { data, setData, processing, errors, put } = useForm({
        name: user.name,
        email: user.email,
        status: user.status ? Number(user.status) : 1,
        role: user.roles && user.roles.length > 0 ? user.roles[0].name : '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(update({ id: user.id }).url);
    };

    const selectedRole = roles.find(role => role.name === data.role);

    // Función auxiliar para bloquear copiar y pegar
    const preventCopyPaste = (e: React.ClipboardEvent<HTMLInputElement>) => {
        e.preventDefault();
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs('Editar usuario', edit({ id: user.id }).url)}>
            <Head title="Editar usuario" />
            <div className="flex flex-col items-center py-6 px-4 sm:px-6 lg:px-8">
                <div className="w-full flex justify-start ml-86">
                    <div className="flex flex-col gap-2 items-start">
                        <h2 className="text-2xl font-bold tracking-tight text-gray-900">
                            Editar usuario
                        </h2>
                        <p className="text-sm text-gray-600 text-right">
                            Actualiza la información y permisos del usuario seleccionado.
                        </p>
                    </div>
                </div>

                <div className="w-full max-w-2xl shadow-2xl border border-gray-100 mt-2 space-y-8 bg-white p-8 rounded-lg">
                    <form onSubmit={handleSubmit} className="mt-8 space-y-6" autoComplete="off">

                        {/* Fila 1: Nombre y Rol */}
                        <div className="flex flex-col sm:flex-row sm:space-x-4 gap-4">
                            <div className="flex-1 flex flex-col gap-2">
                                <Label htmlFor="name">Nombre</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    value={data.name}
                                    required
                                    autoFocus
                                    placeholder="Nombre completo"
                                    autoComplete="off"
                                    onChange={(e) => setData('name', e.target.value)}
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="flex-1 flex flex-col gap-2">
                                <Label htmlFor="role">Rol de Usuario</Label>
                                <Select
                                    value={optionsRoles.find(option => option.value === data.role)}
                                    onChange={(val) => setData('role', val ? String(val.value) : '')}
                                    options={optionsRoles}
                                    inputId="role"
                                    placeholder="Selecciona un rol"
                                    styles={{
                                        control: (provided) => ({
                                            ...provided,
                                            borderColor: errors.role ? '#ef4444' : provided.borderColor,
                                            borderRadius: '0.375rem',
                                        }),
                                    }}
                                />
                                <InputError message={errors.role} />
                            </div>
                        </div>

                        {/* Mostrar Permisos (Opcional, ayuda visual) */}
                        {data.role && selectedRole?.permissions && (
                            <div className="p-3 bg-gray-50 rounded-md border border-gray-100">
                                <p className="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">
                                    Permisos:
                                </p>
                                <div className="flex flex-wrap gap-1.5">
                                    {selectedRole.permissions.map((perm) => (
                                        <span key={perm.id} className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                            {perm.name}
                                        </span>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* Fila 2: Email y Estatus */}
                        <div className="flex flex-col sm:flex-row sm:space-x-4 gap-4">
                            <div className="flex-1 flex flex-col gap-2">
                                <Label htmlFor="email">Correo electrónico</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    placeholder="email@ejemplo.com"
                                    disabled={true} 
                                    className="bg-gray-100 text-gray-500 cursor-not-allowed"
                                    // Aunque está deshabilitado, es buena práctica bloquearlo explícitamente también
                                    onPaste={preventCopyPaste}
                                    onCopy={preventCopyPaste}
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="flex-1 flex flex-col gap-2">
                                <Label htmlFor="status">Estatus</Label>
                                <Select
                                    id="status"
                                    value={optionsStatuses.find(option => option.value === data.status)}
                                    onChange={(val) => setData('status', val ? Number(val.value) : 1)}
                                    options={optionsStatuses}
                                    styles={{
                                        control: (provided) => ({
                                            ...provided,
                                            borderRadius: '0.375rem',
                                        }),
                                    }}
                                />
                            </div>
                        </div>

                        {/* Fila 3: Contraseñas (Opcional) */}
                        <div className="border-t border-gray-100 pt-4">
                            <p className="text-sm text-gray-500 mb-4">Cambiar contraseña (dejar en blanco para mantener la actual)</p>
                            <div className="flex flex-col sm:flex-row sm:space-x-4 gap-4">
                                <div className="flex-1 flex flex-col gap-2">
                                    <Label htmlFor="password">Nueva Contraseña</Label>
                                    <Input
                                        id="password"
                                        type="password"
                                        value={data.password}
                                        placeholder="********"
                                        autoComplete="new-password"
                                        onChange={(e) => setData('password', e.target.value)}
                                        onPaste={preventCopyPaste} // Bloquea pegar
                                        onCopy={preventCopyPaste}  // Bloquea copiar
                                    />
                                    <InputError message={errors.password} />
                                </div>

                                <div className="flex-1 flex flex-col gap-2">
                                    <Label htmlFor="password_confirmation">Confirmar Contraseña</Label>
                                    <Input
                                        id="password_confirmation"
                                        type="password"
                                        value={data.password_confirmation}
                                        placeholder="********"
                                        autoComplete="new-password"
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        onPaste={preventCopyPaste} // Bloquea pegar
                                    />
                                </div>
                            </div>
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

                            <Link
                                href={index().url}
                                className="w-full bg-red-500 text-white hover:bg-red-500 cursor-pointer inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs tracking-widest disabled:opacity-25 transition ease-in-out duration-150 text-center"
                            >
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}