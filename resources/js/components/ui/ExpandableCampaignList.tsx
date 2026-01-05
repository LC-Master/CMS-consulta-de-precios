import { Campaign } from "@/types/campaign/index.types";
import { Link } from "@inertiajs/react";
import { ChevronDown, ChevronUp } from "lucide-react";
import { useState } from "react";
import { show } from "@/routes/campaign";

export default function ExpandableCampaignList({ campaigns }: { campaigns?: Campaign[] }) {
    const [isExpanded, setIsExpanded] = useState(false);
    const maxVisible = 2;
    if (!campaigns || campaigns.length === 0) {
        return <span className="text-gray-400 italic text-xs">Sin asignar</span>;
    }

    const visibleCampaigns = isExpanded ? campaigns : campaigns.slice(0, maxVisible);
    const remainingCount = campaigns.length - maxVisible;
    return (
        <div className="flex flex-col items-start gap-1 min-w-37.5">
            <div className="flex flex-wrap items-center gap-1">
                {visibleCampaigns.map((camp) => (
                    <Link
                        viewTransition
                        key={camp.id}
                        href={show({ id: camp.id }).url}
                        className="underline inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100"
                        title={camp.title}
                    >
                        {camp.title}
                    </Link>
                ))}

                {!isExpanded && remainingCount > 0 && (
                    <button
                        onClick={() => setIsExpanded(true)}
                        className="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-gray-300"
                        title="Clic para ver todas"
                    >
                        +{remainingCount}
                        <ChevronDown className="w-3 h-3" />
                    </button>
                )}
            </div>

            {isExpanded && remainingCount > 0 && (
                <button
                    onClick={() => setIsExpanded(false)}
                    className="text-xs text-gray-500 hover:text-gray-700 underline inline-flex items-center gap-0.5 mt-0.5 focus:outline-none"
                >
                    Ver menos <ChevronUp className="w-3 h-3" />
                </button>
            )}
        </div>
    );
};