import { Dispatch, SetStateAction } from 'react';

/**
 * Hook para gestionar acciones multimedia entre 3 listas independientes:
 * MediaList (General), AM y PM.
 */
export const useMediaActions = <T extends { id: string | number }>() => {
    const moveUp = (id: T['id'], setItems: Dispatch<SetStateAction<T[]>>) => {
        setItems((prev) => {
            const index = prev.findIndex((m) => m.id === id);
            if (index <= 0) return prev;

            const newItems = [...prev];
            [newItems[index], newItems[index - 1]] = [
                newItems[index - 1],
                newItems[index],
            ];
            return newItems;
        });
    };

    const moveDown = (id: T['id'], setItems: Dispatch<SetStateAction<T[]>>) => {
        setItems((prev) => {
            const index = prev.findIndex((m) => m.id === id);
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
    ) => {
        setOrigin((prev) => prev.filter((m) => m.id !== item.id));

        setDestination((prev) => {
            const alreadyExists = prev.some((i) => i.id === item.id);
            if (alreadyExists) return prev;
            return [...prev, item];
        });
    };

    return { moveUp, moveDown, transfer };
};
