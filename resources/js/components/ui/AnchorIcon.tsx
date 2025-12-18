import { LucideIcon } from 'lucide-react'
import { Icon } from '../icon'
import React, { forwardRef } from 'react'
type AnchorIconProps = React.AnchorHTMLAttributes<HTMLAnchorElement> & {
    icon: LucideIcon
    classNameIcon?: string
}

const AnchorIcon = forwardRef<HTMLAnchorElement, AnchorIconProps>(function AnchorIcon(
    { icon, href, className, classNameIcon, ...props },
    ref
) {
    const finalHref = href
    const finalClassName = className ?? 'p-2 bg-locatel-oscuro text-white rounded-md'
    const finalClassNameIcon = classNameIcon ?? 'w-4 h-4'

    return (
        <a ref={ref} href={finalHref} className={finalClassName} {...props}>
            <Icon iconNode={icon} className={finalClassNameIcon} />
        </a>
    )
})

export default AnchorIcon