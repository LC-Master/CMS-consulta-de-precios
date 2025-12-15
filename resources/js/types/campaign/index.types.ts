export interface Department {
    id: string;
    name: string;
}
export interface Center extends Pick<Department, 'id' | 'name'> {
    code: string;
}
export type Agreement = Department;

export interface Option {
    value: string;
    label: string;
}
export interface Status {
    id: string;
    status: string;
}

export interface Campaign {
    id: string;
    title: string;
    status: Status;
    created_at: string;
    [key: string]: unknown;
}

export interface Props {
    campaigns: { data: Campaign[] };
    filters: { search?: string; status?: string };
    statuses: Status[];
}

