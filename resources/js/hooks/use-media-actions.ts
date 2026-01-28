import { Dispatch, SetStateAction } from 'react';

/**
 * Hook para gestionar acciones multimedia entre 3 listas independientes:
 * MediaList (General), AM y PM.
 */
export const useMediaActions = <T extends { id: string | number, instanceId?: string | undefined }>() => {
    const moveUp = (id: T['id'] | T['instanceId'], setItems: Dispatch<SetStateAction<T[]>>) => {
        setItems((prev) => {
            const index = prev.findIndex((m) => m.id === id || m.instanceId === id);

            if (index <= 0) return prev;

            const newItems = [...prev];
            [newItems[index], newItems[index - 1]] = [
                newItems[index - 1],
                newItems[index],
            ];
            return newItems;
        });
    };

    const removeItem = (item: T, setItems: Dispatch<SetStateAction<T[]>>) => {
        setItems((prev) => prev.filter((m) => m.instanceId !== item.instanceId));
    }

    const moveDown = (id: T['id'], setItems: Dispatch<SetStateAction<T[]>>) => {
        setItems((prev) => {
            const index = prev.findIndex((m) => m.id === id || m.instanceId === id);
            if (index === -1 || index >= prev.length - 1) return prev;

            const newItems = [...prev];
            [newItems[index], newItems[index + 1]] = [
                newItems[index + 1],
                newItems[index],
            ];
            return newItems;
        });
    };

    const transfer = (
        item: T,
        setOrigin: Dispatch<SetStateAction<T[]>>,
        setDestination: Dispatch<SetStateAction<T[]>>,
        main = false
    ) => {
        if (!main) {
            setOrigin((prev) => prev.filter((m) => m.id !== item.id));
        }
        setDestination((prev) => {
            // const alreadyExists = prev.some((i) => i.id === item.id);
            // if (alreadyExists) return prev;
            const newItem = {
                ...item,
                instanceId: self.crypto.randomUUID()
            };
            return [...prev, newItem];
        });
    };

    return { moveUp, moveDown, transfer, removeItem };
};
