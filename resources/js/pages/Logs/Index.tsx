import { Column, DataTable } from "@/components/DataTable";
import { Filter } from "@/components/Filter";
import AuditModal from "@/components/modals/AuditModal";
import { Button } from "@/components/ui/button";
import { breadcrumbs } from "@/helpers/breadcrumbs";
import { LEVEL_STYLES, SUBJECT_CONFIG } from "@/helpers/styleModals";
import useModal from "@/hooks/use-modal";
import { useUpdateEffect } from "@/hooks/useUpdateEffect";
import AppLayout from "@/layouts/app-layout";
import { index } from "@/routes/logs";
import { Props, Log } from "@/types/logs/index.type";
import { Head, router } from "@inertiajs/react";
import { Link } from '@inertiajs/react';
import {
    Eye,
} from "lucide-react";
import { useState } from "react";

export default function LogsIndex({ logs, filters }: Props) {
    const [auditLog, setAuditLog] = useState<Log | null>(null);
    const { isOpen, closeModal, openModal } = useModal(false)
    const handleShowAudit = (log: Log) => {
        setAuditLog(log);
        openModal();
    };
    const columns: Column<Log>[] = [
        {
            key: 'level',
            header: 'Prioridad',
            render: (log) => {
                return (
                    <div className={`inline-flex items-center gap-1.5 px-2 py-0.5 rounded border text-[10px] font-black uppercase tracking-wider ${LEVEL_STYLES[log.level]}`}>
                        <span className="h-1.5 w-1.5 rounded-full bg-current"></span>
                        {log.level}
                    </div>
                );
            },
        },
        {
            key: 'subject',
            header: 'Recurso Afectado',
            render: (log) => {
                const config = SUBJECT_CONFIG[log.subject_type];
                return (
                    <div className="flex flex-col gap-0.5">
                        <span className="text-[10px] font-bold text-gray-400 uppercase leading-none">
                            {config?.label || 'SISTEMA'}
                        </span>
                        <Link
                            href={`${config?.url || '#'}/${log.subject_id}/edit`}
                            className="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors truncate max-w-45"
                        >
                            {log.properties?.title || 'Referencia no disponible'}
                        </Link>
                    </div>
                );
            },
        },
        {
            key: 'activity',
            header: 'Detalle de Actividad',
            render: (log) => (
                <div className="flex flex-col group">
                    <p className="text-sm text-gray-700 font-medium leading-snug">
                        {log.message}
                    </p>
                    <div className="flex items-center gap-2 mt-1">
                        <span className="text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded font-mono border border-gray-200">
                            {log.action.toUpperCase()}
                        </span>
                        {log.properties?.changes && (
                            <span className="text-[10px] text-indigo-500 font-semibold opacity-0 group-hover:opacity-100 transition-opacity">
                                • Tiene cambios técnicos
                            </span>
                        )}
                    </div>
                </div>
            ),
        },
        {
            key: 'responsible',
            header: 'Responsable',
            render: (log) => (
                <div className="flex items-center gap-2.5">
                    <div className="flex flex-col">
                        <span className="text-sm font-bold text-gray-800">{log.user?.name || 'Automático'}</span>
                        <div className="flex items-center gap-1 text-[10px] text-gray-400 font-mono">
                            <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {log.ip_address}
                        </div>
                    </div>
                </div>
            ),
        },
        {
            key: 'timestamp',
            header: 'Sincronización',
            render: (log) => (
                <div className="flex flex-col items-center text-center">
                    <span className="text-sm font-bold text-gray-700">
                        {new Date(log.created_at).toLocaleDateString('es-ES', { day: '2-digit', month: 'short' })}
                    </span>
                    <span className="text-[10px] text-gray-400 font-medium">
                        {new Date(log.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                    </span>
                </div>
            ),
        },
        {
            key: 'actions',
            header: 'Auditoria',
            render: (log) => (
                <Button
                    onClick={() => handleShowAudit(log)}
                    className="flex items-center  bg-white justify-center w-8 h-8 rounded-full hover:bg-gray-100 text-gray-400 hover:text-indigo-600 transition-all border border-transparent hover:border-gray-200"
                    title="Ver Auditoría"
                >
                    <Eye className="w-4 h-4" />
                </Button>
            ),
        },
    ];
    const [search, setSearch] = useState(filters.search || '')

    useUpdateEffect(() => {
        router.get(
            window.location.pathname,
            { search },
            { preserveState: true, replace: true, preserveScroll: true }
        )
    }, [search])
    return (
        <AppLayout breadcrumbs={breadcrumbs('Logs de actividad', index().url)}>
            <Head title="Logs de actividad" />
            <AuditModal log={auditLog} isOpen={isOpen} onClose={closeModal} />
            <div className="space-y-4 px-4 pb-4">

                <Filter
                    filters={[
                        {
                            type: 'search',
                            key: 'search',
                            value: search,
                            label: 'Buscar por título',
                            placeholder: 'Buscar por título...',
                            onChange: setSearch,
                        },

                        {
                            type: 'reset',
                            onReset: () => {
                                setSearch('');
                            },
                            key: 'reset',
                        }
                    ]}
                />
                <DataTable
                    data={logs.data}
                    columns={columns}
                    rowKey={(a) => a.id}
                    infiniteData="logs"
                />
            </div >
        </AppLayout>
    );
}