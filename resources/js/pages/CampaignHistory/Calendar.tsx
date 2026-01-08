import { breadcrumbs } from "@/helpers/breadcrumbs";
import AppLayout from "@/layouts/app-layout";
import { show as calendar } from "@/routes/calendar";
import { Head, router } from "@inertiajs/react";
import React from 'react';
import FullCalendar from '@fullcalendar/react';
import dayGridPlugin from '@fullcalendar/daygrid';
import esLocale from '@fullcalendar/core/locales/es';
import { EventInput } from '@fullcalendar/core';
import { show } from "@/routes/campaign";

interface CampaignEvent {
    id: number;
    title: string;
    start: string;
    end: string;
    color: string;
    extendedProps: {
        department: string;
        agreement: string;
    }
}

export default function Calendar({ campaigns }: { campaigns: CampaignEvent[] }) {
    const locatelGreen = "#008a4f";

    const eventInputs: EventInput[] = campaigns.map((c) => ({
        id: String(c.id),
        title: c.title,
        start: c.start,
        end: c.end,
        allDay: true,
        backgroundColor: c.color || locatelGreen,
        borderColor: 'transparent',
        extendedProps: {
            department: c.extendedProps.department,
            agreement: c.extendedProps.agreement,
        },
    }));

    const handleEventClick = ({ event }: { event: { id: string } }) => {
        router.get(show(event.id).url);
    };

    const currentYear = new Date().getFullYear();

    return (
        <AppLayout breadcrumbs={breadcrumbs('Calendario', calendar().url)}>
            <Head title='Calendario Institucional' />
            <div className="py-8 bg-gray-50 min-h-screen">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white shadow-xl rounded-3xl overflow-hidden border-none">

                        <div className="p-8 custom-calendar-container">
                            <FullCalendar
                                plugins={[dayGridPlugin]}
                                initialView="dayGridMonth"
                                locale={esLocale}
                                events={eventInputs}
                                dayMaxEvents={3}
                                moreLinkClick="popover"
                                eventClick={handleEventClick}
                                headerToolbar={{
                                    left: 'today prev,next',
                                    center: 'title',
                                    right: 'dayGridMonth,dayGridWeek'
                                }}
                                eventContent={(eventInfo) => (
                                    <div className="flex flex-col px-2 py-1 overflow-hidden">
                                        <div className="flex items-center gap-1.5">
                                            <div className="w-1.5 h-1.5 rounded-full bg-white/50" />
                                            <span className="text-[10px] font-bold truncate uppercase tracking-tight text-white">
                                                {eventInfo.event.title}
                                            </span>
                                        </div>
                                        <span className="text-[9px] text-white/80 truncate pl-3 font-medium">
                                            {eventInfo.event.extendedProps.department}
                                        </span>
                                    </div>
                                )}
                                validRange={{
                                    start: `${currentYear}-01-01`,
                                    end: `${currentYear}-12-31`
                                }}
                                height="auto"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <style jsx global>{`
                /* 1. RESET Y ESTRUCTURA BASE */
                .fc { --fc-button-bg-color: #f3f4f6; --fc-button-border-color: transparent; --fc-button-hover-bg-color: #e5e7eb; --fc-button-active-bg-color: ${locatelGreen}; --fc-button-active-border-color: transparent; font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; border: none; }
                .fc-theme-tailwind { border: none !important; }
                .fc-scrollgrid { border: none !important; }
                .fc-col-header-cell { background: #f9fafb; padding: 12px 0 !important; font-weight: 600 !important; text-transform: uppercase; font-size: 0.75rem; color: #6b7280; letter-spacing: 0.05em; border: none !important; }
                .fc-daygrid-day { border: 1px solid #f3f4f6 !important; }
                .fc-day-today { background: rgba(0, 138, 79, 0.03) !important; }

                /* 2. DISEÑO DE BARRAS (Píldoras Estilo Locatel) */
                .fc-v-event, .fc-h-event { background: ${locatelGreen}; border: none !important; border-radius: 100px !important; margin: 2px 4px !important; padding: 1px 0 !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
                .fc-event:hover { transform: translateY(-1px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); filter: saturate(1.2); }

                /* 3. CABECERA PERSONALIZADA */
                .fc-toolbar-title { font-size: 1.5rem !important; font-weight: 800 !important; color: #111827 !important; }
                .fc-button { border-radius: 12px !important; font-weight: 600 !important; text-transform: capitalize !important; padding: 8px 16px !important; transition: all 0.2s !important; color: #374151 !important; }
                .fc-button-active { color: white !important; box-shadow: 0 4px 12px rgba(0, 138, 79, 0.3) !important; }
                .fc-today-button { background: white !important; border: 1px solid #e5e7eb !important; opacity: 1 !important; }

                /* 4. POPOVER (+ MÁS) CON SCROLL */
                .fc-popover { background: white !important; border-radius: 20px !important; border: 1px solid #f3f4f6 !important; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important; padding: 0 !important; overflow: hidden !important; width: 300px !important; }
                .fc-popover-header { background: #f9fafb !important; padding: 12px 16px !important; font-weight: 700 !important; border-bottom: 1px solid #f3f4f6 !important; }
                .fc-popover-body { max-height: 300px !important; overflow-y: auto !important; padding: 12px !important; scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
                
                /* Custom scrollbar para Chrome/Safari */
                .fc-popover-body::-webkit-scrollbar { width: 5px; }
                .fc-popover-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

                /* 5. NUMERACIÓN DE DÍAS */
                .fc-daygrid-day-number { font-weight: 700; font-size: 0.9rem; padding: 10px !important; color: #9ca3af; }
                .fc-day-today .fc-daygrid-day-number { color: ${locatelGreen}; font-size: 1.1rem; }

                /* Quitar círculos feos de los eventos */
                .fc-daygrid-event-dot { display: none !important; }
            `}</style>
        </AppLayout>
    );
}