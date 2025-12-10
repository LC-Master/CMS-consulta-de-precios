
export default function AppLogo() {
    return (
        <>
            <div className="flex aspect-square size-24 items-center justify-center rounded-md text-sidebar-primary-foreground">
                <img src="/Logo.webp" alt="CMS Locatel Logo" width={94} height={94} fetchPriority="high"/>
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="mb-0.5 truncate leading-tight font-semibold">
                    CMS Locatel
                </span>
            </div>
        </>
    );
}
