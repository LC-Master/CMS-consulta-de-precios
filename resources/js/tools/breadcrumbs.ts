import { BreadcrumbItem } from '@/types';

export function breadcrumbs(title: string, href: string): BreadcrumbItem[] {
    return [
        {
            title,
            href,
        },
    ];
}
