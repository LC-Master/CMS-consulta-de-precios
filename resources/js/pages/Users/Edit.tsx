import AppLayout from '@/layouts/app-layout';
import { Head, Link, useForm } from '@inertiajs/react';
import { PropsEditPage,  PermissionMatrixItem } from '@/types/user/index.types';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { update, index, edit } from '@/routes/user';
import { breadcrumbs } from '@/helpers/breadcrumbs';
import { groupTranslations } from '@/i18n/permissions';
import { getTranslatedLabel } from '@/helpers/permissions';

import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"

export default function UserEdit({ user, roles, permissions }: PropsEditPage) {
    const getInitialPermissions = () => {
        const direct = user.permissions ? user.permissions.map((p) => p.name) : [];
        const userRoleName = user.roles && user.roles.length > 0 ? user.roles[0].name : null;
        
        let rolePerms: string[] = [];
        if (userRoleName && roles[userRoleName as keyof typeof roles]) {
             const config = roles[userRoleName as keyof typeof roles];
             if (config === '*') {
                 rolePerms = Object.values(permissions).flat().map((p) => p.id);
             } else if (Array.isArray(config)) {
                 rolePerms = config;
             }
        }
        
        return Array.from(new Set([...direct, ...rolePerms]));
    };

    const { data, setData, processing, errors, put } = useForm({
        name: user.name,
        email: user.email,
        role: user.roles && user.roles.length > 0 ? user.roles[0].name : '',
        password: '',
        password_confirmation: '',
        selectedPermissions: getInitialPermissions(),
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(update({ id: user.id }).url);
    };

    const preventCopyPaste = (e: React.ClipboardEvent<HTMLInputElement>) => {
        e.preventDefault();
    };

    const handlePermissionChange = (permissionId: string) => {
        const current = [...data.selectedPermissions];
        const index = current.indexOf(permissionId);

        if (index > -1) {
            current.splice(index, 1);
        } else {
            current.push(permissionId);
        }
        setData('selectedPermissions', current);
    };

    const handleRoleChange = (roleName: string) => {
        const config = roles[roleName as keyof typeof roles];
        let newPerms: string[] = [];
        
        if (config === '*') {
            newPerms = Object.values(permissions).flat().map((p) => p.id);
        } else if (Array.isArray(config)) {
            newPerms = [...config];
        }

        setData((prev) => ({
            ...prev,
            role: roleName,
            selectedPermissions: newPerms
        }));
    };

    const toggleSelectAll = (all: boolean) => {
        if (all) {
             const allIds = Object.values(permissions).flat().map((p) => p.id);
             setData('selectedPermissions', allIds);
        } else {
             setData('selectedPermissions', []);
        }
    };

    const toggleGroup = (groupPerms: PermissionMatrixItem[]) => {
        const groupIds = groupPerms.map(p => p.id);
        const allSelected = groupIds.every(id => data.selectedPermissions.includes(id));
        
        let newSelected = [...data.selectedPermissions];
        
        if (allSelected) {
            newSelected = newSelected.filter(id => !groupIds.includes(id));
        } else {
             const toAdd = groupIds.filter(id => !newSelected.includes(id));
             newSelected = [...newSelected, ...toAdd];
        }
        setData('selectedPermissions', newSelected);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs('Editar usuario', edit({ id: user.id }).url)}>
            <Head title="Editar usuario" />
            <div className="flex flex-col items-center py-6 px-4 sm:px-6 lg:px-8 min-w-92.5">
                <div className="w-full max-w-4xl flex justify-start mb-4">
                    <div className="flex flex-col gap-2 items-start">
                        <h2 className="text-2xl font-bold tracking-tight text-gray-900">
                            Editar usuario
                        </h2>
                        <p className="text-sm text-gray-600">
                            Actualiza la información y gestiona la matriz de permisos.
                        </p>
                    </div>
                </div>

                <div className="w-full max-w-7xl shadow-2xl border border-gray-100 space-y-8 bg-white p-4 sm:p-8 rounded-lg">
                    <form onSubmit={handleSubmit} className="space-y-6" autoComplete="off">

                        {/* SECCIÓN 1: DATOS PERSONALES */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="flex flex-col gap-2">
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

                            <div className="flex flex-col gap-2">
                                <Label htmlFor="email">Correo electrónico (Solo lectura)</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    disabled={true}
                                    className="bg-gray-100 text-gray-500 cursor-not-allowed"
                                />
                            </div>
                        </div>

                        {/* SECCIÓN 2: SEGURIDAD */}
                        <div className="border-t border-gray-100 pt-6">
                            <h3 className="text-sm font-semibold text-gray-900 mb-4">Seguridad</h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="flex flex-col gap-2">
                                    <Label htmlFor="password">Nueva Contraseña</Label>
                                    <Input
                                        id="password"
                                        type="password"
                                        value={data.password}
                                        placeholder="Dejar en blanco para no cambiar"
                                        onChange={(e) => setData('password', e.target.value)}
                                        onPaste={preventCopyPaste}
                                    />
                                    <InputError message={errors.password} />
                                </div>

                                <div className="flex flex-col gap-2">
                                    <Label htmlFor="password_confirmation">Confirmar Contraseña</Label>
                                    <Input
                                        id="password_confirmation"
                                        type="password"
                                        value={data.password_confirmation}
                                        placeholder="Confirmar nueva contraseña"
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        onPaste={preventCopyPaste}
                                    />
                                </div>
                            </div>
                        </div>

                        {/* SECCIÓN 2.5: ASIGNACION DE ROL */}
                        <div className="border-t border-gray-100 pt-6">
                            <h3 className="text-sm font-semibold text-gray-900 mb-4">Rol del Usuario</h3>
                            <div className="max-w-md">
                                <Label className="mb-2 block">Seleccionar Rol (Plantilla de Permisos)</Label>
                                <Select value={data.role} onValueChange={handleRoleChange}>
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="Selecciona un rol" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {Object.keys(roles).map((roleName) => (
                                            <SelectItem key={roleName} value={roleName}>
                                                {roleName.charAt(0).toUpperCase() + roleName.slice(1)}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <p className="text-xs text-gray-500 mt-2">
                                    Al seleccionar un rol, se marcarán automáticamente los permisos asociados. Puedes personalizar la selección después.
                                </p>
                                <InputError message={errors.role} />
                            </div>
                        </div>

                        {/* SECCIÓN 3: MATRIZ DE PERMISOS */}
                        <div className="border-t border-gray-100 pt-8">
                            <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <h3 className="text-lg font-bold text-gray-900">Matriz de Control de Acceso</h3>
                                    <p className="text-xs text-gray-500">Administra las capacidades técnicas del usuario</p>
                                </div>
                                <div className="flex flex-col items-end gap-2">
                                    <div className="flex gap-2 mb-1">
                                         <button type="button" onClick={() => toggleSelectAll(true)} className="text-[10px] font-bold uppercase tracking-wider text-locatel hover:text-locatel-oscuro bg-transparent border-none p-0 cursor-pointer">
                                            Seleccionar Todo
                                         </button>
                                         <span className="text-gray-300 text-[10px]">|</span>
                                         <button type="button" onClick={() => toggleSelectAll(false)} className="text-[10px] font-bold uppercase tracking-wider text-gray-400 hover:text-gray-600 bg-transparent border-none p-0 cursor-pointer">
                                            Limpiar
                                         </button>
                                    </div>
                                </div>
                            </div>

                            <div className="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                                {Object.entries(permissions)
                                    .filter(([group]) => group !== 'token')
                                    .map(([group, perms]) => (
                                    <div key={group} className="mb-8 last:mb-0">
                                        <div className="flex justify-between items-center mb-4 border-b border-gray-100 pb-2">
                                            <h4 className="text-gray-900 text-xs font-black uppercase tracking-[0.2em]">
                                                {groupTranslations[group] || `${group} Management`}
                                            </h4>
                                            <button 
                                                type="button" 
                                                onClick={() => toggleGroup(perms)}
                                                className="text-[10px] uppercase font-bold text-locatel hover:text-locatel-oscuro bg-transparent border-none p-0 cursor-pointer transition-colors"
                                            >
                                                {perms.every((p) => data.selectedPermissions.includes(p.id)) ? 'Deseleccionar grupo' : 'Seleccionar grupo'}
                                            </button>
                                        </div>

                                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                            {perms.map((p) => (
                                                <div
                                                    key={p.id}
                                                    onClick={() => handlePermissionChange(p.id)}
                                                    className={`
                                                        flex flex-row items-center justify-between p-3 rounded-md border transition-all duration-200 group cursor-pointer h-full
                                                        ${data.selectedPermissions.includes(p.id)
                                                            ? 'bg-emerald-50 border-emerald-200 hover:border-emerald-300 shadow-sm'
                                                            : 'bg-white border-gray-100 hover:border-gray-300 hover:shadow-xs'
                                                        }
                                                    `}
                                                >
                                                    <div className="flex flex-col flex-1 mr-2">
                                                        <span className="text-[10px] text-gray-600 uppercase font-bold tracking-wider mb-0.5">
                                                            {getTranslatedLabel(p.id)}
                                                        </span>
                                                        <span className={`text-[9px] font-mono truncate transition-colors ${
                                                            data.selectedPermissions.includes(p.id) ? 'text-emerald-700/80 font-bold' : 'text-gray-500'
                                                        }`}>
                                                            {p.id}
                                                        </span>
                                                    </div>

                                                    <div className="relative inline-flex items-center">
                                                        <input
                                                            type="checkbox"
                                                            className="w-4 h-4 rounded border-gray-300 text-emerald-500 focus:ring-emerald-500 accent-emerald-500 cursor-pointer"
                                                            checked={data.selectedPermissions.includes(p.id)}
                                                            onChange={() => handlePermissionChange(p.id)}
                                                        />
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* BOTONES DE ACCIÓN */}
                        <div className="flex flex-col sm:flex-row gap-3 pt-6">
                            <Button
                                type="submit"
                                className="flex-1 bg-locatel-medio hover:bg-locatel-oscuro text-white hover:opacity-90"
                                disabled={processing}
                            >
                                {processing && <Spinner className="mr-2 h-4 w-4" />}
                                Guardar cambios y desplegar
                            </Button>

                            <Link
                                href={index().url}
                                className="px-6 py-2 bg-gray-100 text-gray-600 rounded-md text-xs font-bold uppercase tracking-widest hover:bg-gray-200 text-center transition-colors"
                            >
                                Descartar
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}