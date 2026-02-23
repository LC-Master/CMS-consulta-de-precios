import { permissionActionTranslations } from "@/i18n/permissions";

export const getTranslatedLabel = (permId: string): string => {
    const parts = permId.split('.');
    const action = parts[parts.length - 1]; // list, create, etc.
    const subAction = parts.length > 2 ? parts[1] : '';

    let label = permissionActionTranslations[action] || action;

    // Casos especiales
    if (subAction === 'history') {
        label = `Historial: ${label}`;
    } else if (subAction === 'placeholder') {
        label = `Placeholder: ${label}`;
    } else if (subAction === 'report') {
        label = `Reporte: ${label}`;
    } else if (subAction === 'sync.url') {
        label = `Sincronización URL: ${label}`;
    } else if (subAction === 'sync') {
        label = `Sincronización: ${label}`;
    }

    return label;
};
