import { LogActionsEnum, LogLevelEnum } from '@/enums/LogsEnum';
import { Flash } from '../flash/flash.type';
import { Department } from "../campaign/index.types"

export interface Log {
    id: string
    action: LogActionsEnum;
    level: LogLevelEnum;
    causer_id: string
    user_name: string
    user_email: string
    message: string
    user_agent: string
    properties: Properties
    ip_address: string
    created_at: string
    subject_type: string
    subject_id: string
}

export interface Properties {
    title: string
    payload: Payload
    changes: Changes
}

export interface Payload {
    id: string
    title: string
    start_at: string
    end_at: string
    status_id: string
    department_id: string
    user_id: string
    updated_by: number
    deleted_at: Date | null
    created_at: string
    updated_at: string
    agreements: Agreement[]
    centers: Center[]
}

export type Agreement = Pick<Department, 'id' | 'name'>

export type Center = Pick<Department, 'id' | 'name'>

export interface Changes {
    before: Before
    after: After
    media_files: MediaFiles
    agreements: Agreements
    centers: Centers
}

export interface Before {
    updated_at: string
}

export interface After {
    updated_at: string
}

export interface MediaFiles {
    before: string[]
    after: string[]
}

export type Agreements = MediaFiles

export type Centers = MediaFiles


export type Props = {
    elements: { value: string; label: string }[];
    flash?: Flash;
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
