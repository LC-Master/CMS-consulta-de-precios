import { useSidebar } from "@/components/ui/sidebar";

export default function AppLogo() {
    const { state } = useSidebar();
    const isCollapsed = state === "collapsed";

    return (
        <>
            {isCollapsed ? (

                <div className="flex items-center justify-center w-full">
                    <img 
                        src="/Logo2.webp" 
                        alt="CMS Locatel Icon" 
                        className="size-8 object-contain" 
                    />
                </div>
            ) : (

                <>
                    {/*  */}
                    <div className="flex aspect-square size-24 items-center justify-center rounded-md text-sidebar-primary-foreground">
                        <img 
                            src="/Logo.webp" 
                            alt="CMS Locatel Logo" 
                            width={94} 
                            height={94} 
                            className="object-contain"
                            fetchPriority="high"
                        />
                    </div>
                    <div className="ml-1 grid flex-1 text-left text-sm">
                        <span className="mb-0.5 truncate leading-tight font-semibold text-gray-700">
                            CMS Locatel
                        </span>
                    </div>
                </>
            )}
        </>
    );
}