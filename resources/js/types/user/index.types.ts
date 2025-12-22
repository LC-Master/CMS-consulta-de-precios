export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    created_at: string;
    updated_at: string;
    status: number;
    roles?: Role[];
}

export interface Props {
    users: { data: User[] };
    filters: { search?: string };
}

export interface Permission {
    id: number;
    name: string;
}

export interface Role {
    id: number;
    name: string;
    permissions: Permission[];
}

export type PropsEditPage = {
    user: User;
    roles: Role[];
}
