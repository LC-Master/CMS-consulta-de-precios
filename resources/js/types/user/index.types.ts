export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    created_at: string;
    updated_at: string;
    status: number;
}

export interface Props {
    users: { data: User[] };
    filters: { search?: string };
}