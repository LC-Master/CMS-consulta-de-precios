import { LogLevelEnum } from "@/enums/LogsEnum";

export const SUBJECT_CONFIG: Record<string, { label: string; url: string; color: string }> = {
    'Campaign': { label: 'Campaña', url: '/campaigns', color: 'text-purple-600' },
    'User': { label: 'Usuario', url: '/users', color: 'text-blue-600' },
    'CenterToken': { label: 'Token', url: '/centers', color: 'text-emerald-600' },
};

export const LEVEL_STYLES: Record<LogLevelEnum, string> = {
    [LogLevelEnum.INFO]: 'bg-blue-100 text-blue-700 border-blue-200',
    [LogLevelEnum.WARNING]: 'bg-yellow-100 text-yellow-700 border-yellow-200',
    [LogLevelEnum.ERROR]: 'bg-orange-100 text-orange-700 border-orange-200',
    [LogLevelEnum.DANGER]: 'bg-red-100 text-red-700 border-red-200',
};