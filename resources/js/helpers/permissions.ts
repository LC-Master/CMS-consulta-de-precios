import { permissionActionTranslations } from "@/i18n/permissions";

export const getTranslatedLabel = (permId: string): string => {
    const parts = permId.split('.');
    const action = parts[parts.length - 1]; // list, create, etc.
    const subAction = parts.length > 2 ? parts[1] : '';

    let label = permissionActionTranslations[action] || action;
    
    // Casos especiales
    if (subAction === 'history') {
        label = `Historial: ${label}`;
    }

    return label;
};
