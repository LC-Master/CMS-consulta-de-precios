import React from 'react';
import Modal from '../Modal';
import { MediaError } from '@/types/store/index.type';
import { AlertCircle, X, CheckCircle, FileWarning, Clock, Hash } from 'lucide-react';

interface MediaErrorsModalProps {
    isOpen: boolean;
    onClose: () => void;
    errors: MediaError[] | undefined;
    storeName?: string;
}

export default function MediaErrorsModal({ isOpen, onClose, errors, storeName }: MediaErrorsModalProps) {
    if (!isOpen) return null;

    return (
        <Modal 
            closeModal={onClose} 
            actionWhenCloseTouchOutside={onClose} 
            blur={true}
            className="p-0 bg-white overflow-hidden max-w-4xl w-full rounded-xl shadow-2xl"
        >
            {/* Header */}
            <div className="bg-red-50/50 px-6 py-4 flex items-center justify-between border-b border-red-100">
                <div className="flex items-center gap-4">
                    <div className="p-2 bg-red-100 rounded-lg text-red-600">
                        <AlertCircle className="w-6 h-6" />
                    </div>
                    <div>
                        <h2 className="text-lg font-bold text-gray-900">Errores de Media</h2>
                        <p className="text-xs text-gray-500 font-medium uppercase tracking-wide">{storeName || 'Tienda'}</p>
                    </div>
                </div>
                <button 
                    onClick={onClose} 
                    className="p-2 rounded-full hover:bg-red-100 text-gray-500 hover:text-red-600 transition-colors"
                >
                    <X className="w-5 h-5" />
                </button>
            </div>

            {/* Content */}
            <div className="max-h-[70vh] overflow-y-auto">
                {!errors || errors.length === 0 ? (
                    <div className="py-16 flex flex-col items-center justify-center text-center text-gray-400">
                        <div className="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mb-4">
                            <CheckCircle className="w-8 h-8 text-green-500" />
                        </div>
                        <h3 className="text-gray-900 font-medium mb-1">Todo en orden</h3>
                        <p className="text-sm">No se encontraron errores de media registrados.</p>
                    </div>
                ) : (
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm text-left">
                            <thead className="bg-gray-50 text-xs text-gray-500 uppercase font-semibold sticky top-0 z-10 shadow-sm">
                                <tr>
                                    <th className="px-6 py-3 whitespace-nowrap">Archivo / Checksum</th>
                                    <th className="px-6 py-3 whitespace-nowrap">Tipo de Error</th>
                                    <th className="px-6 py-3 text-center whitespace-nowrap">Frecuencia</th>
                                    <th className="px-6 py-3 whitespace-nowrap">Última incidencia</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100">
                                {errors.map((error) => (
                                    <tr key={error.id} className="bg-white hover:bg-gray-50 transition-colors group">
                                        <td className="px-6 py-4">
                                            <div className="flex items-start gap-3">
                                                <FileWarning className="w-5 h-5 text-gray-400 mt-0.5 shrink-0 group-hover:text-red-500 transition-colors" />
                                                <div className="min-w-0">
                                                    <p className="font-medium text-gray-900 truncate max-w-50 md:max-w-xs" title={error.name || error.file_name}>
                                                        {error.name || error.file_name || 'Desconocido'}
                                                    </p>
                                                    <div className="flex items-center gap-1 mt-1">
                                                        <Hash className="w-3 h-3 text-gray-300" />
                                                        <code className="text-[10px] text-gray-500 bg-gray-100 px-1 py-0.5 rounded font-mono truncate max-w-30" title={error.checksum}>
                                                            {error.checksum}
                                                        </code>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                                                {error.error_type || error.error_message || 'Error General'}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-center">
                                            <div className="inline-flex flex-col items-center justify-center">
                                                 <span className="text-lg font-bold text-gray-700">{error.error_count || 1}</span>
                                                 <span className="text-[10px] text-gray-400 uppercase">veces</span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center gap-2 text-gray-500">
                                                <Clock className="w-3.5 h-3.5" />
                                                <span className="text-xs whitespace-nowrap">
                                                    {error.last_seen_at 
                                                        ? new Date(error.last_seen_at).toLocaleString('es-VE', { dateStyle: 'medium', timeStyle: 'short' })
                                                        : new Date(error.created_at).toLocaleString('es-VE', { dateStyle: 'medium', timeStyle: 'short' })
                                                    }
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>

            {/* Footer */}
            <div className="bg-gray-50 px-6 py-4 flex justify-end items-center border-t border-gray-200">
                <button
                    onClick={onClose}
                    className="px-6 py-2 bg-white border border-gray-300 hover:bg-gray-50 hover:text-gray-900 text-gray-700 rounded-full font-bold text-sm transition-all shadow-sm hover:shadow"
                >
                    Cerrar listado
                </button>
            </div>
        </Modal>
    );
}
