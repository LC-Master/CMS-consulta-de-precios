import { LucideIcon } from 'lucide-react'
import { Icon } from '../icon'
import { Link } from '@inertiajs/react'
import React, { forwardRef } from 'react'

type AnchorIconProps = {
    icon: LucideIcon
    href: string
    className?: string
    classNameIcon?: string
} & React.ComponentPropsWithoutRef<typeof Link>

const AnchorIcon = forwardRef<HTMLAnchorElement, AnchorIconProps>(function AnchorIcon(
    { icon, href, className, classNameIcon, ...props },
    ref
) {
    const finalHref = href
    const finalClassName = className ?? 'p-2 bg-locatel-oscuro text-white rounded-md'
    const finalClassNameIcon = classNameIcon ?? 'w-4 h-4'

    return (
        <Link viewTransition ref={ref} href={finalHref} className={finalClassName} {...props}>
            <Icon iconNode={icon} className={finalClassNameIcon} />
        </Link>
    )
})

export default AnchorIcon