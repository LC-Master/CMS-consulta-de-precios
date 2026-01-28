export type InitialStats = {
    labels: string[];
    campaigns: number[];
    media: number[];
    totals: {
        campaigns_total: number;
        campaigns_deleted: number;
        media_total: number;
        users_total: number;
        campaigns_active?: number;
        campaigns_pending?: number;
        campaigns_finished?: number;
        avg_campaign_days?: number;
    };
    top_media?: { mime_type: string; count: number }[];
    recent_campaigns?: Array<{ id: string; title: string; created_at: string; start_at: string | null; end_at: string | null; user?: string }>;
};
