export interface SyncState {
    id: string;
    placeholder?: { id: string, mime_type: string };
    last_synced_at?: string;
    sync_status?: string;
    url?: string;
    sync_started_at?: string;
    sync_ended_at?: string;
    disk?: {
        free: number;
        used: number;
        size: number;
    };
    uptimed_at?: string;
    last_reported_at?: string;
}

export interface StoreDetailsModalProps {
    isOpen: boolean;
    onClose: () => void;
    store: Store | null;
}


export interface MediaError {
    id: string;
    store_id: string;
    name: string;
    error_type: string;
    error_count: string;
    checksum: string;
    media_id: string;
    last_seen_at: string;
    created_at: string;
    updated_at: string;
}

export interface Store {
    id: string;
    name: string;
    store_code: string;
    address: string;
    region: string;
    sync_state?: SyncState;
    center_media_errors?: MediaError[];
}
export interface RegionData {
    region: string;
    stores: Store[];
}