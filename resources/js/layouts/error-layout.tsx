export default function ErrorLayout({ children }: { children: React.ReactNode }) {
    return (
        <section className="min-h-screen w-full h-full flex flex-col shadow-lg bg-gray-50">
            <header className="text-center bg-white p-2">
                <img
                    src="/logo.webp"
                    alt="Logo Locatel"
                    className="caret-transparent mx-auto mb-4 h-24 w-auto"
                />
            </header>
            <main className="grow w-full flex-col h-full flex items-center justify-center">
                {children}
            </main>
        </section>
    );
}