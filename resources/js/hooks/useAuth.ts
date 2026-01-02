import { usePage } from '@inertiajs/react';

export type AuthProps = {
    user: User;
    roles: string[];
    permissions: string[];
};

export type User = {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    status: string;
    created_at: string;
    updated_at: string;
    two_factor_confirmed_at: string | null;
    roles: Role[];
};

export type Role = {
    id: number;
    name: string;
    guard_name: string;
    created_at: string;
    updated_at: string;
    pivot: Pivot;
};

export type Pivot = {
    model_type: string;
    model_id: string;
    role_id: string;
};

export default function useAuth() {
    const { auth } = usePage().props as unknown as { auth: AuthProps };

    const Permissions = {
        USER: {
            LIST: 'users.list',
            SHOW: 'users.show',
            CREATE: 'users.create',
            UPDATE: 'users.update',
            DELETE: 'users.delete',
        },
        TOKEN: {
            LIST: 'tokens.list',
            CREATE: 'tokens.create',
            DELETE: 'tokens.delete',
        },
        CAMPAIGN: {
            LIST: 'campaigns.list',
            SHOW: 'campaigns.show',
            CREATE: 'campaigns.create',
            UPDATE: 'campaigns.update',
            DELETE: 'campaigns.delete',
        },
        AGREEMENT: {
            LIST: 'agreements.list',
            SHOW: 'agreements.show',
            CREATE: 'agreements.create',
            UPDATE: 'agreements.update',
            DELETE: 'agreements.delete',
        },
        MEDIA: {
            LIST: 'medias.list',
            SHOW: 'medias.show',
            CREATE: 'medias.create',
            UPDATE: 'medias.update',
            DELETE: 'medias.delete',
        },
        REPORT: {
            VIEW: 'reports.view',
        },
    } as const;

    const hasRole = (role: string): boolean => {
       if (!role.includes('|')) return auth?.roles?.includes(role) ?? false;
       return role.split('|').map(r => auth?.roles?.includes(r) ?? false).includes(true);
    };

    const can = (permission: string): boolean => {
        return auth?.permissions?.includes(permission) ?? false;
    };
    return { hasRole, can, Permissions };
}
