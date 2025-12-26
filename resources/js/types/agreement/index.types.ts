export interface Agreement {
    id: string;
    name: string;
    legal_name: string;
    tax_id: string;
    contact_person: string;
    contact_phone: string;
    is_active: string;
    [key: string]: unknown;
}

export interface Props {
    agreements: { data: Agreement[] };
    filters: { search?: string; status?: string };
    statuses: string[];
}