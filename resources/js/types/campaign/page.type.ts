import { Agreement, Center, Department, MediaItem } from './index.types';

export type CampaignCreateProps = {
    centers: Center[];
    departments: Department[];
    agreements: Agreement[];
    media: MediaItem[];
    flash: { success?: string; error?: string };
};
