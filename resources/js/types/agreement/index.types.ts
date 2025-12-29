export type Agreement = {
    id: string;
    name: string;
    legal_name: string;
    tax_id: string;
    contact_person: string;
    contact_phone: string;
    is_active: boolean;
    contact_email: string;
    start_date: string;
    end_date: string;
    observations?: string;
};

export type Props = {
    agreements: { data: Agreement[] };
    filters: { search?: string; status?: string };
    statuses: string[];
};
