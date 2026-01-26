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
import { Link } from '@inertiajs/react';
import { Users, KeyRound, History, SquarePlus, List, Calendar, Handshake, Film, ChartBar, Logs, Megaphone } from 'lucide-react';
import { lazy } from 'react';
import useAuth from '@/hooks/useAuth';
import { dashboard } from '@/routes';
import { list as reportList } from '@/routes/campaign/report';

const Logo = lazy(() => import('@/components/app-logo'));

export function AppSidebar() {

    const { hasRole, can } = useAuth();

    const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';

    const mainNavItems = hasRole('admin|publicidad|supervisor|consultor') ? [
        can('dashboard.view') ? {
            title: 'Panel de estadísticas',
            url: dashboard().url,
            icon: ChartBar,
        } : undefined,
        {
            title: 'Campañas',
            icon: Megaphone,
            isActive: currentPath.includes('/campaign') || currentPath.includes('/calendar'),
            items: [
                can('campaigns.create') ? {
                    title: 'Crear campaña',
                    url: create().url,
                    icon: SquarePlus,
                } : undefined,
                can('campaigns.list') ? {
                    title: 'Listado de campañas',
                    url: index().url,
                    icon: List,
                } : undefined,
                can('campaigns.history') ? {
                    title: 'Historial',
                    url: indexHistory().url,
                    icon: History,
                } : undefined,
                can('campaigns.history.calendar') ? {
                    title: 'Calendario',
                    url: calendar().url,
                    icon: Calendar,
                } : undefined,
                can('reports.view') ?
                    {
                        title: 'Reportes',
                        url: reportList().url,
                        icon: Calendar,
                    } : undefined,

            ].filter((item) => item !== undefined)
        },
        {
            title: 'Convenios',
            icon: Handshake,
            isActive: currentPath.includes('/agreement'),
            items: [
                can('agreements.create') ? {
                    title: 'Crear Convenios',
                    url: agreementCreate().url,
                    icon: SquarePlus,
                } : undefined,
                can('agreements.list') ? {
                    title: 'Listado de Convenios',
                    url: agreement().url,
                    icon: Handshake,
                } : undefined,
            ].filter((item) => item !== undefined)
        }
    ].filter(item => item !== undefined) : [];

    const adminElement = hasRole('admin|supervisor') ? [
        can('users.list') ? {
            title: 'Usuarios',
            href: user().url,
            icon: Users,
        } : undefined,
        can('tokens.list') ? {
            title: 'Lista de tokens',
            href: tokens().url,
            icon: KeyRound,
        } : undefined,
    ] : [];




    const footerNavItems = [
        can('medias.list') ? {
            title: 'Listado de Medios',
            href: media().url,
            icon: Film,
        } : undefined,
        can('logs.list') ? {
            title: 'Logs de actividad',
            href: indexActivityLog().url,
            icon: Logs,
        } : undefined,
        ...adminElement.filter(item => item !== undefined)
    ].filter((item) => item !== undefined);

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