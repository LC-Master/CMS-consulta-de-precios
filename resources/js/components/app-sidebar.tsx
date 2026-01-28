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

    const { can } = useAuth();

    const currentPath = typeof window !== 'undefined' ? window.location.pathname : '';

    const mainNavItems = [
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
                can('campaign.create') ? {
                    title: 'Crear campaña',
                    url: create().url,
                    icon: SquarePlus,
                } : undefined,
                can('campaign.list') ? {
                    title: 'Listado de campañas',
                    url: index().url,
                    icon: List,
                } : undefined,
                can('campaign.history.view') ? {
                    title: 'Historial',
                    url: indexHistory().url,
                    icon: History,
                } : undefined,
                can('campaign.history.calendar') ? {
                    title: 'Calendario',
                    url: calendar().url,
                    icon: Calendar,
                } : undefined,
                can('report.view') ?
                    {
                        title: 'Reportes',
                        url: reportList().url,
                        icon: Calendar,
                    } : undefined,

            ].filter((item) => item !== undefined)
        },
        {
            title: 'Acuerdos',
            icon: Handshake,
            isActive: currentPath.includes('/agreement'),
            items: [
                can('agreement.create') ? {
                    title: 'Crear Acuerdo',
                    url: agreementCreate().url,
                    icon: SquarePlus,
                } : undefined,
                can('agreement.list') ? {
                    title: 'Listado de Acuerdos',
                    url: agreement().url,
                    icon: Handshake,
                } : undefined,
            ].filter((item) => item !== undefined)
        }
    ].filter(item => item !== undefined);

    const adminElement = [
        can('user.list') ? {
            title: 'Usuarios',
            href: user().url,
            icon: Users,
        } : undefined,
        can('token.list') ? {
            title: 'Lista de tokens',
            href: tokens().url,
            icon: KeyRound,
        } : undefined
    ];


    const footerNavItems = [
        can('media.list') ? {
            title: 'Listado de Medios',
            href: media().url,
            icon: Film,
        } : undefined,
        can('log.list') ? {
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