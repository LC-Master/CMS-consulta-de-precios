import { LogActionsEnum, LogLevelEnum } from '@/enums/LogsEnum';

export type Log = {
    id: string;
    subject_id: string;
    subject_type: string;
    user_id: string;
    action: LogActionsEnum;
    level: LogLevelEnum;
    message: string;
    properties: Properties;
    ip_address: string;
    user_agent: string;
    referer: string;
    created_at: string;
    causer_id?: string;
    user_name?: string;
    user_email?: string;
};

export type Properties = {
    title: string;
    changes: Changes;
};

export type Changes = {
    status_id: string;
    updated_by: number;
    updated_at: string;
};


export type Props = {
    elements: { value: string; label: string }[];
    flash?: { success?: string; error?: string };
    logs: { data: Log[] };
    filters: {
        element?: string;
        search?: string;
        status?: string;
        type?: string;
        ended_at?: string;
        started_at?: string;
    };
};
