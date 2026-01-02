import { ChangeEvent, useMemo, useState } from 'react';

export default function useSearch<T extends { name: string }>(items: T[]) {
    const [search, setSearch] = useState<string>('');

    const handlerSearch = (e: ChangeEvent<HTMLInputElement>) => {
        setSearch(e.target.value);
    };
    const filteredItems = useMemo(() => {
        if (!search) return items; 
        
        return items.filter((item) =>
            item.name.toLowerCase().includes(search.toLowerCase())
        );
    }, [search, items]);

    return { search, handlerSearch, filteredItems };
}