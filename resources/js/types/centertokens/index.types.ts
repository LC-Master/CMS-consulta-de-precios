import { Center } from '@/types/campaign/index.types';
export type Props = {
    centerTokens: { data: CenterToken[] };
    centers: Center[];
    flash?: { success?: string; error?: string };
    filters: { search?: string; center?: string };
};

export type CenterToken = {
    id: number;
    name: string;
    token: string;
    abilities: string[];
    last_used_at: string | null;
    created_at: string;
    center: Center;
};
