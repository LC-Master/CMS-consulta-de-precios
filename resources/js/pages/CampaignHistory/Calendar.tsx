import { breadcrumbs } from "@/helpers/breadcrumbs";
import AppLayout from "@/layouts/app-layout";
import { show as calendar } from "@/routes/calendar";
import { Head, router } from "@inertiajs/react";
import { useState, useMemo, useRef, useCallback } from 'react';
import FullCalendar from '@fullcalendar/react';
import dayGridPlugin from '@fullcalendar/daygrid';
import esLocale from '@fullcalendar/core/locales/es';
import { EventInput, DatesSetArg } from '@fullcalendar/core';
import { show } from "@/routes/campaign";
import { CampaignEvent } from "@/types/calendar/index.type";
import { Button } from "@/components/ui/button";
import { FileSpreadsheet, Loader2 } from 'lucide-react';
import { toPng } from 'html-to-image';
import axios from 'axios';

export default function Calendar({ campaigns }: { campaigns: CampaignEvent[] }) {
    const locatelGreen = "#008a4f";
    const [selectedCenter, setSelectedCenter] = useState<string>('all');
    const [currentViewDate, setCurrentViewDate] = useState<Date>(new Date());
    const [isExporting, setIsExporting] = useState(false);
    
    const calendarRef = useRef<HTMLDivElement>(null);

    const centersList = useMemo(() => {
        const all = campaigns.flatMap(c => c.extendedProps.centers || []);
        return ['all', ...Array.from(new Set(all))].filter(Boolean);
    }, [campaigns]);

    const centerColors = useMemo(() => {
        const palette = [
            '#dc2626', '#ea580c', '#d97706', '#65a30d', 
            '#059669', '#7c3aed', '#c026d3', '#db2777', 
            '#e11d48', '#854d0e',
        ];
        
        const uniqueCenters = centersList.filter(c => c !== 'all');
        return uniqueCenters.reduce((acc, center, i) => {
            acc[center] = palette[i % palette.length];
            return acc;
        }, {} as Record<string, string>);
    }, [centersList]);

    const filteredEvents: EventInput[] = useMemo(() => {
        return campaigns
            .filter(c => selectedCenter === 'all' || c.extendedProps.centers.includes(selectedCenter))
            .map((c) => {
                const targetCenter = selectedCenter === 'all' ? (c.extendedProps.centers[0] || '') : selectedCenter;
                const bgColor = centerColors[targetCenter] || locatelGreen;

                return {
                    id: String(c.id),
                    title: c.title,
                    start: c.start,
                    end: c.end,
                    allDay: true,
                    backgroundColor: bgColor,
                    borderColor: 'transparent',
                    extendedProps: {
                        department: c.extendedProps.department,
                        agreement: c.extendedProps.agreement,
                        centers: c.extendedProps.centers
                    },
                };
            });
    }, [campaigns, selectedCenter, centerColors]);

    const handleEventClick = ({ event }: { event: { id: string } }) => {
        router.get(show({ id: event.id }).url);
    };

    const handleExportImage = async () => {
        if (!calendarRef.current) return;
        setIsExporting(true);

        try {
            await new Promise(r => setTimeout(r, 100));

            const imageBase64 = await toPng(calendarRef.current, {
                cacheBust: true,
                pixelRatio: 1.5, 
                backgroundColor: 'white',
                skipFonts: true,
                fontEmbedCSS: '', 
                filter: (node) => {
                    const exclusionClasses = ['hide-on-export'];
                    if (node.tagName === 'IMG' || (node.classList && node.classList.contains('hide-on-export'))) {
                        return false;
                    }
                    return true;
                }
            });

            const response = await axios.post('/campaigns/calendar/export', {
                image: imageBase64
            }, {
                responseType: 'blob',
            });

            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            
            const contentDisposition = response.headers['content-disposition'];
            let fileName = 'calendario.xlsx';
            if (contentDisposition) {
                const fileNameMatch = contentDisposition.match(/filename="?([^"]+)"?/);
                if (fileNameMatch && fileNameMatch.length === 2)
                    fileName = fileNameMatch[1];
            }
            
            link.setAttribute('download', fileName);
            document.body.appendChild(link);
            link.click();
            
            link.remove();
            window.URL.revokeObjectURL(url);
            
            setIsExporting(false);

        } catch (error) {
            console.error("Error exportando:", error);
            setIsExporting(false);
            alert("Hubo un error al descargar el archivo. Verifica la consola para más detalles.");
        }
    };

    const validRange = useMemo(() => {
        const year = new Date().getFullYear();
        return {
            start: `${year}-01-01`,
            end: `${year}-12-31`
        };
    }, []);

    const headerToolbar = useMemo(() => ({
        left: 'title',
        center: '',
        right: 'today prev,next'
    }), []);

 
    const handleDatesSet = useCallback((dateInfo: DatesSetArg) => {
        const middleDate = new Date(
            (dateInfo.start.getTime() + dateInfo.end.getTime()) / 2
        );
        
       setCurrentViewDate((prevDate) => {
            if (middleDate.getMonth() !== prevDate.getMonth() || 
                middleDate.getFullYear() !== prevDate.getFullYear()) {
                return middleDate;
            }
            return prevDate;
        });
    }, []);

    return (
        <AppLayout breadcrumbs={breadcrumbs('Calendario', calendar().url)}>
            <Head title='Calendario de Campañas' />
            <div className="py-8 bg-white min-h-screen">
                <div className="max-w-400 mx-auto px-4">

                    <div className="mb-8 p-6 bg-gray-50 rounded-2xl border border-gray-100">
                        <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                            <h3 className="text-sm font-black text-[#008a4f] uppercase tracking-tighter">
                                Filtrar por Centro
                            </h3>
                            
                            <Button 
                                onClick={handleExportImage}
                                disabled={isExporting}
                                className="bg-locatel-medio hover:bg-locatel-oscuro text-white flex items-center gap-2"
                            >
                                {isExporting ? <Loader2 className="w-4 h-4 animate-spin" /> : <FileSpreadsheet className="w-4 h-4" />}
                                {isExporting ? 'Generando...' : 'Exportar a Excel'}
                            </Button>
                        </div>

                        <div className="flex flex-wrap gap-3">
                            {centersList.map(name => {
                                if(name === 'Todo') return null;

                                const isSelected = selectedCenter === name;
                                const btnColor = name === 'all' ? locatelGreen : (centerColors[name] || locatelGreen);

                                return (
                                    <Button
                                        key={name}
                                        onClick={() => setSelectedCenter(name)}
                                        style={{
                                            backgroundColor: isSelected ? btnColor : 'white',
                                            borderColor: isSelected ? btnColor : '#e5e7eb',
                                            color: isSelected ? 'white' : '#374151'
                                        }}
                                        className={`px-5 py-2.5 rounded-xl text-xs font-bold transition-all border shadow-sm ${
                                            isSelected 
                                                ? 'shadow-lg' 
                                                : 'hover:bg-gray-50'
                                        }`}
                                    >
                                        {name === 'all' ? 'TODOS LOS CENTROS' : name.toUpperCase()}
                                    </Button>
                                );
                            })}
                        </div>
                    </div>

                    <div ref={calendarRef} className="calendar-card shadow-2xl border border-gray-200 rounded-xl overflow-hidden bg-white p-4">
                        <div className="mb-4 text-center">
                            <h2 className="text-xl font-bold text-gray-800">Calendario de Campañas</h2>
                            <p className="text-sm text-gray-500">
                                {currentViewDate.toLocaleString('es-ES', { month: 'long', year: 'numeric' }).toUpperCase()} 
                                {' - '} 
                                {selectedCenter === 'all' ? 'Todos los centros' : selectedCenter}
                            </p>
                        </div>

                        <FullCalendar
                            plugins={[dayGridPlugin]}
                            initialView="dayGridMonth"
                            locale={esLocale}
                            events={filteredEvents}
                            dayMaxEvents={4}
                            moreLinkClick="popover"
                            eventClick={handleEventClick}
                            datesSet={handleDatesSet}
                            headerToolbar={headerToolbar}
                            validRange={validRange}
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
                            height="auto"
                        />
                    </div>
                </div>
            </div>
            
            <style>{`
                .fc-header-toolbar {
                    background: #f8fafc;
                    margin: 0 !important;
                    padding: 20px 30px !important;
                    border-bottom: 2px solid #e5e7eb;
                }
                .fc-toolbar-title {
                    font-size: 1.2rem !important;
                    font-weight: 900 !important;
                    color: #111827 !important;
                    text-transform: capitalize;
                    white-space: nowrap;
                }
                .fc .fc-button {
                    background: #ffffff !important;
                    border: 2px solid #e5e7eb !important;
                    color: #111827 !important;
                    font-weight: 800 !important;
                    font-size: 0.75rem !important;
                    padding: 8px 16px !important;
                    border-radius: 8px !important;
                    margin-left: 8px !important; 
                }
                .fc .fc-button:hover { border-color: #008a4f !important; color: #008a4f !important; }
                .fc .fc-button-primary:not(:disabled).fc-button-active {
                    background: #008a4f !important;
                    border-color: #008a4f !important;
                    color: white !important;
                }
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
                .fc-h-event {
                    border-radius: 6px !important;
                    margin: 2px 4px !important;
                    padding: 2px 0 !important;
                    border: none !important;
                }
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
