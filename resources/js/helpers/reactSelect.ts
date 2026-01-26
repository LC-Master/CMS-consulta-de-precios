import { Option } from '@/types/campaign/index.types';

export function makeOptions<T>(
    list: T[],
    getValue: (item: T) => string,
    getLabel: (item: T) => string,
): Option[] {
    return list.map((item) => ({
        value: getValue(item),
        label: getLabel(item),
    }));
}
