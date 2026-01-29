import { Flash } from "../flash/flash.type";

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    created_at: string;
    updated_at: string;
    status: number;
    roles: Role[];
    permissions?: Permission[];
    deleted_at?: string | null;
}

export interface Props {
    users: { data: User[] };
    filters: { search?: string };
    flash: Flash;
}
export type Permission = {
    id: number | string;
    name: string;
    label?: string;
    guard_name?: string;
    created_at?: string;
    updated_at?: string;
}

export interface PermissionMatrixItem {
    id: string;
    name: string;
    label: string;
}

export type PermissionMatrix = Record<string, PermissionMatrixItem[]>;

export interface Role {
    id: number;
    name: string;
    permissions: Permission[];
}

export type PropsEditPage = {
    user: User;
    permissions: PermissionMatrix;
    statuses: { name: string; value: number }[];
    roles: Record<string, string[] | string>; // roles config from config('permissions.roles')
    flash?: Flash;
};

export type PropsCreatePage = Pick<PropsEditPage, 'roles'> & { permissions: PermissionMatrix, flash?: Flash };