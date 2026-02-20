export const formatBytes = (bytes: number | string, decimals = 2) => {
    const value = Number(bytes);
    if (!value) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(value) / Math.log(k));
    return `${parseFloat((value / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
}
export function mediaNameNormalizer(name: string) {
    const cleanName = name.replace(/_/g, ' ').toLowerCase().split('.')[0];

    return cleanName.charAt(0).toUpperCase() + cleanName.slice(1);
}
export function formatDate(date?: string | number | Date, options: Intl.DateTimeFormatOptions = {}) {
    if (!date) return "-";
    return new Date(date).toLocaleString([], options);
}

export function isVideo(mime_type: string) {
    return mime_type.startsWith("video");
}
export const formatForEdit = (dateStr: string | Date) => {
    if (!dateStr) return '';

    const localDate = new Date(dateStr).toLocaleString('sv-SE', {
        timeZone: 'America/Caracas'
    });

    return localDate.replace(' ', 'T').substring(0, 16);
};