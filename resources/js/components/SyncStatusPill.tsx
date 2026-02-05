import React from 'react';
import { SYNC_STATUS_TRANSLATIONS, SYNC_STATUS_STYLES } from '@/i18n/sync-status';

interface SyncStatusPillProps {
    status: string | null | undefined;
    className?: string;
    showIcon?: boolean; 
}

export default function SyncStatusPill({ status, className = ""}: SyncStatusPillProps) {
    const statusKey = status || 'default';
    const label = SYNC_STATUS_TRANSLATIONS[statusKey] || SYNC_STATUS_TRANSLATIONS['default'];
    const styleClass = SYNC_STATUS_STYLES[statusKey] || SYNC_STATUS_STYLES['default'];

    return (
        <span className={`px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border ${styleClass} ${className}`}>
            {label}
        </span>
    );
}