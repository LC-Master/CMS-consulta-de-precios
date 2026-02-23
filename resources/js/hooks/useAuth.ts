import { User } from '@/types/user/index.types';
import { usePage } from '@inertiajs/react';

export type AuthProps = {
    user: User;
    roles: string[];
    permissions: string[];
};

export default function useAuth() {
    const { auth } = usePage().props as unknown as { auth: AuthProps };
    const hasRole = (role: string): boolean => {
        if (!role.includes('|')) return auth?.roles?.includes(role) ?? false;
        return role
            .split('|')
            .map((r) => auth?.roles?.includes(r) ?? false)
            .includes(true);
    };

    const can = (permission: string): boolean => {
        return (auth?.permissions?.includes(permission) || auth.user.permissions?.some(p => p.name === permission)) ?? false;
    };
    return { hasRole, can, Permissions };
}
