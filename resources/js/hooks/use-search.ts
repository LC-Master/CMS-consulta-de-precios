import { ChangeEvent, useState } from 'react';

export default function useSearch<T extends { name: string }>(
    items: T[],
    setItems: React.Dispatch<React.SetStateAction<T[]>>,
) {
    const [search, setSearch] = useState<string>('');
    const handlerSearch = (e: ChangeEvent<HTMLInputElement>) => {
        setSearch(e.target.value);
        setItems(
            items.filter((item) =>
                item.name.toLowerCase().includes(e.target.value.toLowerCase()),
            ) || [],
        );
    };
    return { search, handlerSearch };
}
