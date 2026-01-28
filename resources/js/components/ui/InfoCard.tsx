import { cn } from "@/lib/utils";
import { ReactNode } from "react";

interface InfoCardProps extends Omit<React.HTMLAttributes<HTMLDivElement>, "title"> {
    title?: ReactNode;
    icon?: ReactNode;
    headerEnd?: ReactNode;
    contentClassName?: string;
    headerClassName?: string;
}

export function InfoCard({
    title,
    icon,
    headerEnd,
    children,
    className,
    contentClassName,
    headerClassName,
    ...props
}: InfoCardProps) {
    return (
        <div
            className={cn(
                "bg-white rounded-xl shadow shadow-stone-400 m-2",
                className
            )}
            {...props}
        >
            {(title || icon || headerEnd) && (
                <div
                    className={cn(
                        "flex items-center justify-between border-b border-gray-300 p-6",
                        headerClassName
                    )}
                >
                    <div className="flex items-center gap-4">
                        {icon}
                        {typeof title === "string" ? (
                            <h2 className="text-lg font-semibold">{title}</h2>
                        ) : (
                            title
                        )}
                    </div>
                    {headerEnd}
                </div>
            )}
            <div className={contentClassName}>{children}</div>
        </div>
    );
}
