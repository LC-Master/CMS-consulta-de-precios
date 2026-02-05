import { makeOptions } from '@/helpers/makeOptions';
import { Agreement } from '@/types/agreement/index.types';
import {
    Department,
    Option,
} from '@/types/campaign/index.types';

export default function useLoadOptions(
    departments: Department[],
    agreements: Agreement[],
) {

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
    return { optionsDepartment, optionsAgreement };
}
