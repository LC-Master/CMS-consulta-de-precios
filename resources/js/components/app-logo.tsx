import { useSidebar } from "./ui/sidebar";

export default function AppLogo() {
    const { open } = useSidebar()
    console.log(open)
    return (
        <>
            <div className="flex aspect-square w-24 items-center justify-center rounded-md text-sidebar-primary-foreground transition-all duration-300 ease-in-out">
                {
                    open ? (
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
                    )
                }
            </div>
            {open && (
                <div className="ml-1 grid flex-1 text-left text-sm transition-opacity duration-300 ease-in-out">
                    <span className="mb-0.5 truncate leading-tight font-semibold">
                        CMS Locatel
                    </span>
                </div>
            )}
        </>
    );
}
