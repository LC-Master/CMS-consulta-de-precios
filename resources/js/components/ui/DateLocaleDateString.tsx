export default function DateLocaleDateString({ className }: { className?: string }) {
    return (
        <span
            className={className ?? 'bg-white p-2 rounded-lg flex items-center gap-2'}
            aria-label={new Date().toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            })}
        >
            <p className="ml-auto text-sm block md:hidden">
                {new Date().toLocaleDateString('es-ES', {
                    day: 'numeric',
                    month: 'short',
                })}
            </p>

            <p className="ml-auto text-sm hidden md:block whitespace-nowrap">
                {new Date().toLocaleDateString('es-ES', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                })}
            </p>
        </span>
    )
}
