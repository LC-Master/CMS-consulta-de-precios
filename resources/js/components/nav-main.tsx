"use client"

import { ChevronRight, type LucideIcon } from "lucide-react"
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from "@/components/ui/collapsible"
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { Link, usePage } from '@inertiajs/react';

interface NavItem {
    title: string;
    url?: string;
    href?: string;
    icon?: LucideIcon;
    isActive?: boolean;
    items?: {
        title: string;
        url: string;
    }[];
}

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const page = usePage();

    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel className='text-white'>Navegación</SidebarGroupLabel>
            <SidebarMenu>
                {items.map((item) => {
                    const mainUrl = item.url || item.href || "#";

                    if (item.items && item.items.length > 0) {
                        return (
                            <Collapsible
                                key={item.title}
                                asChild
                                defaultOpen={item.isActive}
                                className="group/collapsible"
                            >
                                <SidebarMenuItem>
                                    <CollapsibleTrigger asChild>
                                        <SidebarMenuButton
                                            tooltip={item.title}
                                            className="h-10 text-sm transition-colors hover:bg-white hover:text-black data-[state=open]:bg-white data-[state=open]:text-black active:bg-white active:text-black"
                                        >
                                            {item.icon && <item.icon />}
                                            <span className="font-medium">{item.title}</span>
                                            <ChevronRight className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                        </SidebarMenuButton>
                                    </CollapsibleTrigger>
                                    <CollapsibleContent>
                                        <SidebarMenuSub className="mt-2 space-y-1 border-l-white/30">
                                            {item.items.map((subItem) => (
                                                <SidebarMenuSubItem key={subItem.title}>
                                                    <SidebarMenuSubButton
                                                        asChild
                                                        isActive={page.url === subItem.url}
                                                        className="h-9 text-sm transition-colors hover:bg-white hover:text-black text-white data-[active=true]:bg-white data-[active=true]:text-black rounded-md"
                                                    >
                                                        <Link href={subItem.url} prefetch>
                                                            <span>{subItem.title}</span>
                                                        </Link>
                                                    </SidebarMenuSubButton>
                                                </SidebarMenuSubItem>
                                            ))}
                                        </SidebarMenuSub>
                                    </CollapsibleContent>
                                </SidebarMenuItem>
                            </Collapsible>
                        )
                    }

                    return (
                        <SidebarMenuItem key={item.title}>
                            <SidebarMenuButton
                                asChild
                                isActive={page.url === mainUrl}
                                tooltip={{ children: item.title }}
                                className="h-10 text-sm transition-colors hover:bg-white hover:text-black data-[active=true]:bg-white data-[active=true]:text-black"
                            >
                                <Link href={mainUrl} prefetch>
                                    {item.icon && <item.icon />}
                                    <span className="font-medium">{item.title}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )
                })}
            </SidebarMenu>
        </SidebarGroup>
    );
}