export interface Store {
    id: string;
    name: string;
    store_code: string;
    address: string;
}
export interface RegionData {
    region: string;
    stores: Store[];
}