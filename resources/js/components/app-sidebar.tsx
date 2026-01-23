import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { create as agreementCreate, index as agreement } from '@/routes/agreement';
import { index, create } from '@/routes/campaign';
import { index as indexActivityLog } from '@/routes/logs';
import { history as indexHistory } from '@/routes/campaignsHistory';
import { show as calendar } from '@/routes/calendar';
import { index as media } from '@/routes/media';
import { index as user } from '@/routes/user';
import { index as tokens } from '@/routes/centertokens';
import { type NavItem } from '@/types'; // Asegúrate de que esto apunte a tu index.d.ts o types.ts
import { Link } from '@inertiajs/react';
import { Users, KeyRound, History, SquarePlus, List, Calendar, Handshake, Film, ChartBar, Logs, Megaphone } from 'lucide-react';
import { lazy } from 'react';
import useAuth from '@/hooks/useAuth';
import { dashboard } from '@/routes';

const Logo = lazy(() => import('@/components/app-logo'));

export function AppSidebar() {

    const { hasRole } = useAuth();
    
    const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';

    // MainNav usa 'url' porque actualizamos NavMain para soportarlo
    const mainNavItems = hasRole('admin|publicidad') ? [
        {
            title: 'Panel de estadísticas',
            url: dashboard().url,
            icon: ChartBar,
        },
        {
            title: 'Campañas',
            icon: Megaphone, 
            isActive: currentPath.includes('/campaign') || currentPath.includes('/calendar'), 
            items: [
                {
                    title: 'Crear campaña',
                    url: create().url,
                    icon: SquarePlus,
                },
                {
                    title: 'Listado de campañas',
                    url: index().url,
                    icon: List,
                },
                {
                    title: 'Historial',
                    url: indexHistory().url,
                    icon: History,
                },
                {
                    title: 'Calendario',
                    url: calendar().url,
                    icon: Calendar,
                }
            ]
        },
        {
            title: 'Convenios',
            icon: Handshake,
            isActive: currentPath.includes('/agreement'),
            items: [
                {
                    title: 'Crear Convenios',
                    url: agreementCreate().url,
                    icon: SquarePlus,
                },
                {
                    title: 'Listado de Convenios',
                    url: agreement().url,
                    icon: Handshake,
                }
            ]
        }
    ] : [];

    const adminElement = hasRole('admin') ? [
        {
            title: 'Usuarios',
            href: user().url, 
            icon: Users,
        },
        {
            title: 'Lista de tokens',
            href: tokens().url, 
            icon: KeyRound,
        },
    ] : [];

    const footerNavItems = [
        {
            title: 'Listado de Medios',
            href: media().url,
            icon: Film,
        },
        {
            title: 'Logs de actividad',
            href: indexActivityLog().url,
            icon: Logs,
        },
        ...adminElement
    ];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader className="bg-locatel-medio rounded-t-lg">
                <SidebarMenu className='bg-white rounded-lg'>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={index().url} prefetch>
                                <Logo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent className='bg-locatel-medio text-white'>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter className='bg-locatel-medio rounded-b-lg text-white'>
                <NavFooter items={footerNavItems} className="mt-auto " />
                <NavUser />
            </SidebarFooter>
        </Sidebar >
    );
}