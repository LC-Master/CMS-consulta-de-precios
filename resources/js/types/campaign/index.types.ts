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
