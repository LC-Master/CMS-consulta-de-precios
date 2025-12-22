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
import { dashboard, } from '@/routes';
import { store, index as agreement } from '@/routes/agreement';
import { index, create } from '@/routes/campaign';
import { index as user } from '@/routes/user';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { Users, Folder, LayoutGrid, SquarePlus, List, Handshake } from 'lucide-react';
import { lazy } from 'react';

const Logo = lazy(() => import('@/components/app-logo'));

const mainNavItems: NavItem[] = [
    {
        title: 'Crear campaña',
        href: create().url,
        icon: SquarePlus,
    }
    , {
        title: 'Campañas',
        href: index().url,
        icon: List,
    }
    , {
        title: 'Crear Convenios',
        href: store().url,
        icon: SquarePlus,
    }
    , {
        title: 'Convenios',
        href: agreement().url,
        icon: Handshake,
    }
    , {
        title: 'Panel de control',
        href: dashboard(),
        icon: LayoutGrid,
    },

];

const footerNavItems: NavItem[] = [
    {
        title: 'Repositorio',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Usuarios',
        href: user().url,
        icon: Users,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader className="bg-locatel-medio rounded-t-lg">
                <SidebarMenu className='bg-white rounded-lg'>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
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
