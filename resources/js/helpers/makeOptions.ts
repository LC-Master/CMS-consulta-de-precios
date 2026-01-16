import { Option } from '@/types/campaign/index.types';

export const makeOptions = <T>(
    items: T[],
    valueFn: (item: T) => string,
    labelFn: (item: T) => string,
): Option[] =>
    items.map((item) => ({ value: valueFn(item), label: labelFn(item) }));
