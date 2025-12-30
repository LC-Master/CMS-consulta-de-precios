export default function MediaFallBack() {
    return (
        <div className="flex flex-col items-center justify-center gap-4 p-6 w-full">
            <div className="flex flex-col items-center gap-4 p-6">
                <div className="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center animate-pulse" />
                <div className="w-40 h-4 rounded bg-gray-200 animate-pulse" />
                <div className="w-80 h-12 rounded bg-gray-200 animate-pulse" />
                <span className="text-sm text-gray-600">Cargando vista previa…</span>
            </div>
        </div>
    );
}