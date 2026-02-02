import { Flash } from '../flash/flash.type';
import { Store } from '../store/index.type';

export type Props = {
    centerTokens: { data: CenterToken[] };
    stores: Store[];
    flash?: Flash;
    filters: { search?: string; store?: string };
};

export type CenterToken = {
    id: number;
    name: string;
    token: string;
    abilities: string[];
    last_used_at: string | null;
    created_at: string;
    store: Store;
};
