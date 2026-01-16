import { Log } from "@/types/logs/index.type";
import { ShieldCheck, X, Activity } from "lucide-react";
import Modal from "../Modal";

export default function AuditModal({ log, isOpen, onClose }: { log: Log | null, isOpen: boolean, onClose: () => void }) {
    if (!isOpen || !log) return null;

    return (
        <Modal closeModal={onClose} >
            <div className="px-5 py-3 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">
                <div className="flex items-center gap-2">
                    <ShieldCheck size={18} />
                    <span className="text-xs font-bold text-gray-900 uppercase tracking-tight">Registro Técnico</span>
                </div>
                <button onClick={onClose} className="text-gray-400 hover:text-gray-600 transition-colors">
                    <X size={18} />
                </button>
            </div>

            <div className="overflow-y-auto max-h-[70vh]">
                <div className="p-5 bg-slate-50/50 border-b border-gray-100">
                    <p className="text-[10px] uppercase font-bold text-slate-400 mb-1">Actividad</p>
                    <p className="text-sm font-semibold text-slate-700 leading-snug">{log.message}</p>
                </div>

                <div className="grid grid-cols-2 divide-x divide-gray-100 border-b border-gray-100 bg-white">
                    <div className="p-3 px-5">
                        <p className="text-[9px] uppercase font-bold text-slate-400">Responsable</p>
                        <p className="text-xs font-bold text-slate-600 truncate">{log.user?.name || 'Sistema'}</p>
                    </div>
                    <div className="p-3 px-5">
                        <p className="text-[9px] uppercase font-bold text-slate-400">IP Origen</p>
                        <p className="text-xs font-mono font-bold text-slate-600">{log.ip_address}</p>
                    </div>
                </div>

                <div className="p-5">
                    <h4 className="text-[10px] uppercase font-black text-slate-400 tracking-widest mb-4 flex items-center gap-2">
                        <Activity size={12} /> Cambios realizados
                    </h4>

                    <div className="space-y-3">
                        {log.properties?.changes ? (
                            Object.entries(log.properties.changes).map(([key, value]: [string, { old: string; new: string }]) => (
                                <div key={key} className="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-sm">
                                    {/* Nombre del Campo */}
                                    <div className="bg-slate-50 px-3 py-1.5 border-b border-slate-200">
                                        <span className="text-[10px] font-black text-slate-500 uppercase font-mono italic">
                                            {key.replace(/_/g, ' ')}
                                        </span>
                                    </div>

                                    <div className="p-3 space-y-2">
                                        {/* Valor Anterior */}
                                        <div className="flex items-start gap-2 opacity-60">
                                            <span className="text-[9px] font-bold text-red-500 bg-red-50 px-1 rounded mt-0.5">OLD</span>
                                            <p className="text-xs font-mono text-slate-500 line-through truncate">
                                                {String(value.old ?? 'n/a')}
                                            </p>
                                        </div>

                                        {/* Valor Nuevo */}
                                        <div className="flex items-start gap-2">
                                            <span className="text-[9px] font-bold text-emerald-500 bg-emerald-50 px-1 rounded mt-0.5">NEW</span>
                                            <p className="text-xs font-mono text-slate-800 font-bold wrap-break-words">
                                                {String(value.new)}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <p className="text-center text-xs text-slate-400">Sin detalles de cambios.</p>
                        )}
                    </div>
                </div>
            </div>

            <div className="p-4 bg-gray-50/50 border-t border-gray-100 flex justify-end">
                <button
                    onClick={onClose}
                    className="px-4 py-2 bg-slate-900 text-white text-[10px] font-bold uppercase rounded-lg hover:bg-black transition-all active:scale-95"
                >
                    Entendido
                </button>
            </div>
        </Modal>
    );
};