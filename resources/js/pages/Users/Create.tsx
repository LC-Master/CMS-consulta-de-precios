import { useMemo } from 'react';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { useForm } from '@inertiajs/react';

import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

interface Permission {
    id: number;
    name: string;
}

interface Role {
    id: number;
    name: string;
    permissions: Permission[];
}

interface Props {
    roles: Role[];
}

export default function UserCreate({ roles }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Crear usuario',
            href: '/user/create',
        },
    ];

    const { data, setData, processing, errors, post } = useForm({
        name: '',
        email: '',
        role: '',
        password: '',
        password_confirmation: '',
    });

    const selectedRoleInfo = useMemo(() => {
        return roles.find(r => r.name === data.role);
    }, [data.role, roles]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/user');
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            {/* CAMBIO 1: py-12 a py-6 para subir el formulario y reducir el scroll */}
            <div className="flex flex-col items-center justify-center py-6 px-4 sm:px-6 lg:px-8">
                
                {/* CAMBIO 2: max-w-md a max-w-2xl para hacerlo más ancho */}
                <div className="w-full max-w-2xl space-y-8 bg-white p-8 shadow rounded-lg">
                    
                    <div className="text-center">
                        <h2 className="text-2xl font-bold tracking-tight text-gray-900">
                            Registrar nuevo usuario
                        </h2>
                        <p className="mt-2 text-sm text-gray-600">
                            Completa la información para dar de alta un usuario
                        </p>
                    </div>

                    <form onSubmit={handleSubmit} className="mt-8 space-y-6" autoComplete="off">
                        <input type="text" style={{ display: 'none' }} />
                        <input type="password" style={{ display: 'none' }} />

                        {/* Nombre */}
                        <div className="grid gap-2">
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

                        {/* Email */}
                        <div className="grid gap-2">
                            <Label htmlFor="email">Correo electrónico</Label>
                            <Input
                                id="email"
                                type="email"
                                value={data.email}
                                required
                                placeholder="email@ejemplo.com"
                                autoComplete="new-email"
                                name="new-email-field"
                                readOnly={true}
                                onFocus={(e) => e.target.readOnly = false}
                                onChange={(e) => setData('email', e.target.value)}
                            />
                            <InputError message={errors.email} />
                        </div>

                        {/* Selector de Rol */}
                        <div className="grid gap-2">
                            <Label htmlFor="role">Rol de Usuario</Label>
                            <select
                                id="role"
                                value={data.role}
                                required
                                onChange={(e) => setData('role', e.target.value)}
                                className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="" disabled>Selecciona un rol</option>
                                {roles.map((role) => (
                                    <option key={role.id} value={role.name}>
                                        {role.name}
                                    </option>
                                ))}
                            </select>
                            <InputError message={errors.role} />

                            {/* Caja de Permisos */}
                            {selectedRoleInfo && (
                                <div className="mt-2 p-3 bg-gray-50 rounded-md border border-gray-100 animate-in fade-in slide-in-from-top-1 duration-300">
                                    <p className="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wider">
                                        Permisos incluidos:
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

                        {/* Contraseña */}
                        <div className="grid gap-2">
                            <Label htmlFor="password">Contraseña</Label>
                            <Input
                                id="password"
                                type="password"
                                value={data.password}
                                required
                                placeholder="********"
                                autoComplete="new-password"
                                name="new-password-field"
                                readOnly={true}
                                onFocus={(e) => e.target.readOnly = false}
                                onChange={(e) => setData('password', e.target.value)}
                            />
                            <InputError message={errors.password} />
                        </div>

                        {/* Confirmar Contraseña */}
                        <div className="grid gap-2">
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

                        {/* Botones */}
                        <div className="flex flex-col gap-3 pt-4">
                            <Button
                                type="submit"
                                className="w-full bg-locatel-oscuro text-white hover:bg-locatel-oscuro cursor-pointer"
                                disabled={processing}
                            >
                                {processing && <Spinner className="mr-2 h-4 w-4" />}
                                Guardar Usuario
                            </Button>

                            <Button
                                type="button"
                                className="w-full bg-red-500 text-white hover:bg-red-500 cursor-pointer"
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