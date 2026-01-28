import { Agreement } from '../agreement/index.types';
import { Flash } from '../flash/flash.type';
import { Status } from '../status/status.type';
import { Center, Department, MediaItem } from './index.types';

export type CampaignReportProps = {
    flash?: Flash;
    departments: Department[];
    agreements: Agreement[];
    statuses: Status[];
};

export type CampaignCreateProps = {
    centers: Center[];
    departments: Department[];
    agreements: Agreement[];
    media: MediaItem[];
    flash: Flash;
};
export type CampaignEditProps = {
    campaign: CampaignFormValues;
    statuses: Status[];
    departments: Department[];
    agreements: Agreement[];
    media: MediaItem[];
    centers: Center[];
    flash: Flash;
};
export type CampaignFormValues = {
    id: string;
    title: string;
    start_at: string;
    end_at: string;
    status_id: string;
    department_id: string;
    agreements: Pick<Agreement, 'id' | 'name'>[];
    media: MediaItem[];
    created_at: string;
    updated_at: string;
    centers: Center[];
};
