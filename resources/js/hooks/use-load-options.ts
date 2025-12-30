import { makeOptions } from '@/helpers/makeOptions';
import {
    Agreement,
    Center,
    Department,
    Option,
} from '@/types/campaign/index.types';

export default function useLoadOptions(
    centers: Center[],
    departments: Department[],
    agreements: Agreement[],
) {
    const optionsCenter: Option[] = makeOptions<Center>(
        centers,
        (c) => String(c.id),
        (c) => `${c.name} - ${c.code}`,
    );
    const optionsDepartment: Option[] = makeOptions<Department>(
        departments,
        (d) => String(d.id),
        (d) => d.name,
    );
    const optionsAgreement: Option[] = makeOptions<Agreement>(
        agreements,
        (a) => String(a.id),
        (a) => a.name,
    );
    return { optionsCenter, optionsDepartment, optionsAgreement };
}
