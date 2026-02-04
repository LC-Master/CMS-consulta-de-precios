export interface CampaignEvent {
    id: string;
    title: string;
    start: string;
    end: string;
    color: string;
    extendedProps: {
        department: string;
        agreement: string;
        centers: string[];
        store_ids?: string[];
    }
}