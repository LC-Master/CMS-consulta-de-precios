import { z } from 'zod';

export const reportFormSchema = z
    .object({
        start_at: z.coerce.date({ error: 'La fecha de inicio es obligatoria' }),
        end_at: z.coerce.date({ error: 'La fecha de fin es obligatoria' }),
        department_id: z
            .uuid({ error: 'El departamento es inválido' })
            .optional()
            .or(z.literal('')),
        agreement_id: z
            .uuid({ error: 'El acuerdo es inválido' })
            .optional()
            .or(z.literal('')),
        status_id: z
            .uuid({ error: 'El estado es inválido' })
            .optional()
            .or(z.literal('')),
    })
    .refine((data) => data.start_at <= data.end_at, {
        message:
            'La fecha de inicio debe ser anterior o igual a la fecha de fin',
        path: ['end_at'],
    })
    .refine((data) => data.end_at >= data.start_at, {
        message:
            'La fecha de fin debe ser posterior o igual a la fecha de inicio',
        path: ['start_at'],
    });
