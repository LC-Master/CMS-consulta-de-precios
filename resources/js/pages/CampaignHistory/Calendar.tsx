import { breadcrumbs } from "@/helpers/breadcrumbs";
import AppLayout from "@/layouts/app-layout";
import { show as calendar } from "@/routes/calendar";
import { Head, Link, router } from "@inertiajs/react";
import React, { useState, useMemo } from 'react';
import FullCalendar from '@fullcalendar/react';
import dayGridPlugin from '@fullcalendar/daygrid';
import esLocale from '@fullcalendar/core/locales/es';
import { EventInput } from '@fullcalendar/core';
import { index, show } from "@/routes/campaign";
import { CampaignEvent } from "@/types/calendar/index.type";

export default function Calendar({ campaigns }: { campaigns: CampaignEvent[] }) {
    const locatelGreen = "#008a4f";
    const [selectedCenter, setSelectedCenter] = useState<string>('all');

    const centersList = useMemo(() => {
        const all = campaigns.flatMap(c => c.extendedProps.centers || []);
        return ['all', ...Array.from(new Set(all))].filter(Boolean);
    }, [campaigns]);

    const filteredEvents: EventInput[] = useMemo(() => {
        return campaigns
            .filter(c => selectedCenter === 'all' || c.extendedProps.centers.includes(selectedCenter))
            .map((c) => ({
                id: String(c.id),
                title: c.title,
                start: c.start,
                end: c.end,
                allDay: true,
                backgroundColor: locatelGreen,
                borderColor: 'transparent',
                extendedProps: {
                    department: c.extendedProps.department,
                    agreement: c.extendedProps.agreement,
                    centers: c.extendedProps.centers
                },
            }));
    }, [campaigns, selectedCenter]);

    const handleEventClick = ({ event }: { event: { id: string } }) => {
        router.get(show(event.id).url);
    };

    const currentYear = new Date().getFullYear();

    return (
        <AppLayout breadcrumbs={breadcrumbs('Calendario', calendar().url)}>
            <Head title='Calendario de Campañas' />
            <div className="py-8 bg-white min-h-screen">
                <div className="max-w-400 mx-auto px-4">

                    <div className="mb-8 p-6 bg-gray-50 rounded-2xl border border-gray-100">
                        <h3 className="text-sm font-black text-[#008a4f] uppercase tracking-tighter mb-4">
                            Filtrar por Centro
                        </h3>
                        <div className="flex flex-wrap gap-3">
                            {centersList.map(name => (
                                <button
                                    key={name}
                                    onClick={() => setSelectedCenter(name)}
                                    className={`px-5 py-2.5 rounded-xl text-xs font-bold transition-all ${selectedCenter === name
                                        ? 'bg-[#008a4f] text-white shadow-lg'
                                        : 'bg-white text-gray-700 border border-gray-200 hover:border-[#008a4f]'
                                        }`}
                                >
                                    {name === 'all' ? 'TODOS LOS CENTROS' : name.toUpperCase()}
                                </button>
                            ))}
                        </div>
                    </div>

                    <div className="calendar-card shadow-2xl border border-gray-200 rounded-xl overflow-hidden bg-white">
                        <FullCalendar
                            plugins={[dayGridPlugin]}
                            initialView="dayGridMonth"
                            locale={esLocale}
                            events={filteredEvents}
                            dayMaxEvents={4}
                            moreLinkClick="popover"
                            eventClick={handleEventClick}
                            headerToolbar={{
                                left: 'title',
                                center: '',
                                right: 'today prev,next'
                            }}
                            eventContent={(eventInfo) => (
                                <div className="flex flex-col w-full px-2 py-1 leading-[1.1] overflow-hidden text-white">
                                    <span className="text-[10px] font-black uppercase truncate leading-none">
                                        {eventInfo.event.title}
                                    </span>
                                    <span className="text-[9px] font-medium opacity-90 truncate leading-none mt-0.5">
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
                    <Link viewTransition href={index().url} className="mt-6 p-4 rounded-2xl inline-block text-sm text-white bg-locatel-medio hover:bg-locatel-oscuro hover:text-white">&larr; Volver</Link>
                </div>
            </div>

            <style >{`
                /* NAVEGACIÓN CON ESPACIADO REAL */
                .fc-header-toolbar {
                    background: #f8fafc;
                    margin: 0 !important;
                    padding: 20px 30px !important;
                    border-bottom: 2px solid #e5e7eb;
                }

                .fc-toolbar-title {
                    font-size: 1.5rem !important;
                    font-weight: 900 !important;
                    color: #111827 !important;
                    text-transform: capitalize;
                }

                .fc .fc-button {
                    background: #ffffff !important;
                    border: 2px solid #e5e7eb !important;
                    color: #111827 !important;
                    font-weight: 800 !important;
                    font-size: 0.75rem !important;
                    padding: 8px 16px !important;
                    border-radius: 8px !important;
                    margin-left: 8px !important; /* Espacio entre botones de navegación */
                }

                .fc .fc-button:hover { border-color: #008a4f !important; color: #008a4f !important; }
                .fc .fc-button-primary:not(:disabled).fc-button-active {
                    background: #008a4f !important;
                    border-color: #008a4f !important;
                    color: white !important;
                }

                /* DISEÑO DE TABLA (ALTO CONTRASTE) */
                .fc-scrollgrid { border: 2px solid #e5e7eb !important; }
                
                .fc-col-header-cell {
                    background: #111827 !important;
                    padding: 12px 0 !important;
                    border: 1px solid #374151 !important;
                }
                .fc-col-header-cell-cushion {
                    color: #ffffff !important;
                    font-weight: 800 !important;
                    text-transform: uppercase;
                    font-size: 0.7rem;
                }

                .fc-daygrid-day-number {
                    color: #111827 !important;
                    font-weight: 900 !important;
                    font-size: 0.95rem;
                    padding: 10px !important;
                }
                
                .fc-day-today { background: #ecfdf5 !important; }
                .fc-day-today .fc-daygrid-day-number { color: #008a4f !important; }

                /* EVENTOS */
                .fc-h-event {
                    border-radius: 6px !important;
                    margin: 2px 4px !important;
                    padding: 2px 0 !important;
                    border: none !important;
                }

                /* POPOVER CON SCROLL */
                .fc-popover {
                    border-radius: 12px !important;
                    border: 2px solid #111827 !important;
                    box-shadow: 10px 10px 0px rgba(0,0,0,0.05) !important;
                }
                .fc-popover-header { background: #111827 !important; color: white !important; font-weight: 900 !important; }
                .fc-popover-body { max-height: 300px !important; overflow-y: auto !important; }
            `}</style>
        </AppLayout>
    );
}