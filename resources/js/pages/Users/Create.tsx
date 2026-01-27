import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { PropsCreatePage } from '@/types/user/index.types';
import { store } from '@/routes/user';
import Select from 'react-select';
import { breadcrumbs } from '@/helpers/breadcrumbs';
import { index } from '@/routes/user';

export default function UserCreate({ roles }: PropsCreatePage) {
    const optionsRoles = roles.map((role) => ({ value: role.name, label: role.name }))
    
    const { data, setData, processing, errors, post } = useForm({
        name: '',
        role: '',
        email: '',
        email_confirmation: '', // Nuevo campo
        password: '',
        password_confirmation: '',
    });

    const selectedRole = roles.find(role => role.name === data.role);

    return (
        <AppLayout breadcrumbs={breadcrumbs('Crear Usuario', index().url)}>
            <Head title="Crear Usuario" />
            <div className="grid place-items-center py-6 px-4 sm:px-6 lg:px-8">
                <div className="grid gap-2">
                    <h2 className="text-2xl font-bold tracking-tight text-gray-900">
                        Registrar un nuevo usuario
                    </h2>
                    <p className="mt-2 text-sm text-gray-600">
                        Complete los campos a continuación para registrar un nuevo miembro al equipo.
                    </p>
                </div>

                <div className="w-full max-w-2xl shadow-2xl border border-gray-100 mt-2 space-y-8 bg-white p-8 rounded-lg">
                    <form onSubmit={(e: React.FormEvent) => {
                        e.preventDefault();
                        post(store().url);
                    }} className="mt-8 space-y-6" autoComplete="off">

                        {/* BLOQUE 1: Nombre y Rol */}
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
                                    name="role"
                                    classNamePrefix="react-select"
                                    placeholder="Selecciona un rol"
                                    isClearable
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

                        {/* Mostrar permisos del rol seleccionado (Visual) */}
                        {data.role && (
                            <div className="p-3 bg-gray-50 rounded-md border border-gray-100 animate-in fade-in slide-in-from-top-1 duration-300">
                                <p className="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">
                                    Permisos incluidos:
                                </p>
                                {selectedRole?.permissions?.length ? (
                                    <div className="flex flex-wrap gap-1.5">
                                        {selectedRole.permissions.map((perm) => (
                                            <span key={perm.id} className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                                {perm.name}
                                            </span>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-xs text-gray-400 italic">Este rol no tiene permisos asignados.</p>
                                )}
                            </div>
                        )}

                        {/* BLOQUE 2: Correo y Confirmación */}
                        <div className="flex flex-col sm:flex-row sm:space-x-4 gap-4">
                            <div className="flex-1 flex flex-col gap-2">
                                <Label htmlFor="email">Correo electrónico</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    required
                                    placeholder="email@ejemplo.com"
                                    autoComplete="new-email"
                                    name="new-email-field"
                                    onChange={(e) => setData('email', e.target.value)}
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="flex-1 flex flex-col gap-2">
                                <Label htmlFor="email_confirmation">Confirmar Correo electrónico</Label>
                                <Input
                                    id="email_confirmation"
                                    type="email"
                                    value={data.email_confirmation}
                                    required
                                    placeholder="Repita el correo"
                                    autoComplete="off"
                                    onChange={(e) => setData('email_confirmation', e.target.value)}
                                    // Validación visual simple: borde rojo si no coinciden y hay texto
                                    className={data.email && data.email_confirmation && data.email !== data.email_confirmation ? "border-red-500 focus-visible:ring-red-500" : ""}
                                />
                            </div>
                        </div>

                        {/* BLOQUE 3: Contraseñas */}
                        <div className="flex flex-col sm:flex-row sm:space-x-4 gap-4">
                            <div className="flex-1 flex flex-col gap-2">
                                <Label htmlFor="password">Contraseña</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    value={data.password}
                                    required
                                    placeholder="********"
                                    autoComplete="new-password"
                                    name="new-password-field"
                                    onChange={(e) => setData('password', e.target.value)}
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="flex-1 flex flex-col gap-2">
                                <Label htmlFor="password_confirmation">Confirmar Contraseña</Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    value={data.password_confirmation}
                                    required
                                    placeholder="********"
                                    autoComplete="new-password"
                                    onChange={(e) => setData('password_confirmation', e.target.value)}
                                />
                            </div>
                        </div>

                        <div className="grid gap-3 pt-4">
                            <Button
                                type="submit"
                                className="w-full bg-locatel-oscuro text-white hover:bg-locatel-oscuro cursor-pointer"
                                disabled={processing}
                            >
                                {processing && <Spinner className="mr-2 h-4 w-4" />}
                                Guardar Usuario
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