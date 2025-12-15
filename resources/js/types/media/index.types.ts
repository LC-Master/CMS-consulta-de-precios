export interface Campaign {
    id: string;
    title: string; 
}
export interface Media {
    id: string;
    name: string;
    mime_type: string;
    size: string;
    duration_seconds: string;

    campaigns?: Campaign[];
    [key: string]: unknown;
}

export interface Props {
    medias: { data: Media[] };
    filters: { search?: string; };
}