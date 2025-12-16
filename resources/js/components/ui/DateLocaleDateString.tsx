export default function DateLocaleDateString({ className }: { className?: string }) {
    return (
        <span className={`${className ? className : 'bg-white p-2 rounded-lg flex items-center gap-2'}`}>
            <p className="ml-auto text-s hidden md:block">
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
