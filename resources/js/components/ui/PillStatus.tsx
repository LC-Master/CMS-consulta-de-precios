import { StatusCampaignEnum } from "@/enums/statusCampaignEnum";
import { cn } from "@/lib/utils";
import { cva } from "class-variance-authority";
import { Trash } from "lucide-react";

const pillVariants = cva(
    "inline-flex items-center px-2 text-xs leading-5 font-semibold rounded-full border-[0.9px]",
    {
        variants: {
            status: {
                "Activa": "text-locatel-medio bg-green-200 border-locatel-claro",
                "Finalizada": "text-blue-600 bg-blue-200 border-blue-400",
                "Borrador": "text-yellow-600 bg-yellow-200 border-yellow-400",
                "Inhabilitada": "text-red-600 bg-red-200 border-red-400",
                "Cancelada": "text-red-700 bg-red-100 border-red-300",
                "default": "text-gray-600 bg-gray-200 border-gray-400",
            },
        },
        defaultVariants: {
            status: "default",
        },
    }
);

export function PillStatus({ status }: { status: typeof StatusCampaignEnum[keyof typeof StatusCampaignEnum] | "default" | null | undefined }) {
    return (
        <span className={cn(pillVariants({ status: status }))}>
            {status === "Inhabilitada" ? (<Trash className=" h-3 w-3 mr-1" />) : (
                <svg className="mr-1 h-2 w-2 fill-current" viewBox="0 0 8 8">
                    <circle cx="4" cy="4" r="3" />
                </svg>
            )}
            {status ?? "-"}
        </span>
    );
}