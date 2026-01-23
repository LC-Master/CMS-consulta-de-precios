import { Server } from "lucide-react";

export default function ({ id, name, code }: { id: string; name: string; code: string }) {
    return (<div
        key={id}
        className="flex-none w-56 md:w-54 h-20 rounded-lg border border-transparent hover:border-blue-300 px-4 py-3 flex items-center gap-3 mx-3 shadow-lg transition-colors"
    >
        <span className="bg-gray-100 w-12 h-12 rounded-full flex items-center justify-center" aria-hidden="true">
            <Server className="w-6 h-6 text-gray-500" />
        </span>
        <div className="overflow-hidden">
            <p className="font-medium text-sm truncate">{name}</p>
            <p className="text-xs text-gray-500 truncate">{code}</p>
        </div>
    </div>)
}