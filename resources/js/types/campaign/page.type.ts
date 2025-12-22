import { Agreement, Center, Department, MediaItem } from './index.types';

export type CampaignCreateProps = {
    centers: Center[];
    departments: Department[];
    agreements: Agreement[];
    media: MediaItem[];
    flash: { success?: string; error?: string };
};
export type CampaignEditProps = {
    campaign: CampaignFormValues;
    statuses: { id: number; status: string }[];
    departments: Department[];
    agreements: Agreement[];
    media: MediaItem[];
    centers: Center[];
    flash: { success?: string; error?: string };
};
export type CampaignFormValues = {
    id: number;
    title: string;
    start_at: string;
    end_at: string;
    status_id: string;
    department_id: string;
    agreement_id: string;
    media: MediaItem[];
    created_at: string;
    updated_at: string;
};
