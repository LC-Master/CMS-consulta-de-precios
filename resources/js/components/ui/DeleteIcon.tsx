import { LucideIcon } from 'lucide-react'
import { Link } from '@inertiajs/react'
import { Icon } from '../icon'
export default function DeleteIcon({ url, icon, className, classNameIcon }: { url: string, icon: LucideIcon, className?: string, classNameIcon?: string }) {
    return (
        <Link
            href={url}
            className={`${className ? className : 'p-2 bg-locatel-naranja text-white rounded-md'}`}
        >
            <Icon iconNode={icon} className={`${classNameIcon ? classNameIcon : "w-4 h-4"}`} />
        </Link>
    )
}