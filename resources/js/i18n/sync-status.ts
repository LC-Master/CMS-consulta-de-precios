export const SYNC_STATUS_TRANSLATIONS: Record<string, string> = {
    'pending': 'PENDIENTE',
    'syncing': 'SINCRONIZANDO',
    'success': 'SINCRONIZADO',
    'synced': 'SINCRONIZADO',
    'completed': 'COMPLETADO',
    'failed': 'FALLIDO',
    'error': 'ERROR',
    'stale': 'OBSOLETO',
    'default': 'DESCONOCIDO'
};

export const SYNC_STATUS_STYLES: Record<string, string> = {
    'pending': 'bg-yellow-100 text-yellow-700 border-yellow-200',
    'syncing': 'bg-blue-100 text-blue-700 border-blue-200',
    'success': 'bg-green-100 text-green-700 border-green-200',
    'synced': 'bg-green-100 text-green-700 border-green-200',
    'completed': 'bg-green-100 text-green-700 border-green-200',
    'failed': 'bg-red-100 text-red-700 border-red-200',
    'error': 'bg-red-100 text-red-700 border-red-200',
    'stale': 'bg-gray-100 text-gray-700 border-gray-200',
    'default': 'bg-gray-100 text-gray-500 border-gray-200'
};