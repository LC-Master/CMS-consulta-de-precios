export interface SyncState {
    id: string;
    placeholder?: { id: string, mime_type: string };
    last_synced_at?: string;
    sync_status?: string;
    url?: string;
    sync_started_at?: string;
    sync_ended_at?: string;
    disk?: string;
    uptimed_at?: string;
    last_reported_at?: string;
}

export interface StoreDetailsModalProps {
    isOpen: boolean;
    onClose: () => void;
    store: Store | null;
}


export interface MediaError {
    id: number;
    store_id: number;
    file_name: string;
    error_message: string;
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