import { z } from 'zod';

export const processZodErrors = (
    error: z.ZodError,
    setErrorFn: (errs: Record<string, string>) => void,
) => {
    const fieldErrors = z.flattenError(error).fieldErrors;
    const normalized = Object.keys(fieldErrors).reduce<Record<string, string>>(
        (acc, key) => {
            const val = fieldErrors[key as keyof typeof fieldErrors];
            acc[key] = Array.isArray(val) ? (val[0] ?? '') : '';
            return acc;
        },
        {},
    );
    setErrorFn(normalized);
};
