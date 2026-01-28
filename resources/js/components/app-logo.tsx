import { useSidebar } from "./ui/sidebar";
import { useEffect, useState } from "react";

export default function AppLogo() {
    const { open } = useSidebar();
    const [isPhone, setIsPhone] = useState(false);

    useEffect(() => {
        if (typeof window === "undefined") return;
        const mql = window.matchMedia("(max-width: 640px)");
        const update = () => setIsPhone(mql.matches);
        update();
        if (mql.addEventListener) mql.addEventListener("change", update);
        else mql.addListener(update);
        return () => {
            if (mql.removeEventListener) mql.removeEventListener("change", update);
            else mql.removeListener(update);
        };
    }, []);

    const showLargeLogo = open || isPhone;

    return (
        <>
            <div className="flex aspect-square w-24 items-center justify-center rounded-md text-sidebar-primary-foreground transition-all duration-300 ease-in-out">
                {showLargeLogo ? (
                    <img
                        src="/Logo.webp"
                        alt="CMS Locatel Logo"
                        className="w-24 h-24 object-contain transition-opacity duration-300 ease-in-out transform scale-100"
                        fetchPriority="high"
                    />
                ) : (
                    <img
                        src="/Logo2.webp"
                        alt="CMS Locatel Logo"
                        className="w-8 h-8 object-cover transition-opacity duration-300 ease-in-out transform scale-95"
                        fetchPriority="high"
                    />
                )}
            </div>
            {showLargeLogo && (
                <div className="ml-1 grid flex-1 text-left text-sm transition-opacity duration-300 ease-in-out">
                    <span className="mb-0.5 truncate leading-tight font-semibold">
                        CMS Locatel
                    </span>
                </div>
            )}
        </>
    );
}
