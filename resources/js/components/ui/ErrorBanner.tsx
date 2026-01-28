import { X } from "lucide-react";

export default function ErrorBanner({ message, description }: { message: string; description: string }) {
    return (
        <div className="w-full flex flex-col items-center justify-center py-8 px-6 text-center space-y-3">
            <div className="bg-gray-100 text-red-500 rounded-full p-3 inline-flex items-center justify-center">
                <X className="w-6 h-6" />
            </div>
            <h3 className="text-lg font-semibold">{message}</h3>
            <p className="text-sm text-gray-500">{description}</p>
        </div>
    )
} 