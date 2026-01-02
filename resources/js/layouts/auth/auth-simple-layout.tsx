import AppLogoIcon from '@/components/app-logo-icon';
import { index } from '@/routes/agreement';
// import { home } from '@/routes';
import { Link } from '@inertiajs/react';
import { type PropsWithChildren } from 'react';

interface AuthLayoutProps {
    name?: string;
    title?: string;
    description?: string;
}

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: PropsWithChildren<AuthLayoutProps>) {
    return (
        <div className="flex min-h-svh flex-col items-center bg-background justify-center gap-6  p-2 md:p-4 ">
            <div className="w-full p-5 max-w-sm rounded-lg bg-white dark:bg-slate-800 shadow-[0_0_18px_rgba(0,0,0,0.08)] dark:shadow-[0_0_18px_rgba(0,0,0,0.35)] ring-1 ring-inset ring-gray-100/60 transition-shadow duration-200 ease-in-out hover:shadow-[0_0_28px_rgba(0,0,0,0.12)]">
                <div className="flex flex-col gap-8">
                    <div className="flex flex-col items-center">
                        <Link
                            href={index().url}
                            className="flex flex-col items-center font-medium"
                        >
                            <div className="flex h-42 w-98 items-center justify-center">
                                <AppLogoIcon className="h-36 w-98" />
                            </div>
                            <span className="sr-only">{title}</span>
                        </Link>

                        <div className="space-y-2 text-center">
                            <h1 className="text-xl font-medium">{title}</h1>
                            <p className="text-center text-sm text-muted-foreground">
                                {description}
                            </p>
                        </div>
                    </div>
                    {children}
                </div>
            </div>
        </div>
    );
}
