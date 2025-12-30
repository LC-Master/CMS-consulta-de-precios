import { MonitorX, RotateCw } from "lucide-react";

export default function MediaError({ onRetry }: { onRetry: () => void; }) {
    return (
        <div className="flex flex-col items-center gap-4 p-6">
            <div className="w-16 h-16 flex items-center justify-center bg-red-100 rounded-full">
                <MonitorX className='text-red-600' />
            </div>
            <h2 className="text-lg font-semibold">No se pudo cargar el medio</h2>
            <p className="text-sm text-gray-600 w-80 text-center wrap-break-words">hubo un problema al conectar con el servidor. Por Favor, verifique su conexión e intente nuevamente.</p>
            <div className="flex gap-2">
                <button onClick={onRetry} className="flex flex-row items-center gap-2 px-3 py-2 bg-white border border-black rounded">
                    <RotateCw />
                    Reintentar
                </button>
            </div>
        </div>
    );
}