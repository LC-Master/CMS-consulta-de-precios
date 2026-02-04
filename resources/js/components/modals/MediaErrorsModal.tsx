import React from 'react';
import Modal from '../Modal';
import { MediaError } from '@/types/store/index.type';

interface MediaErrorsModalProps {
    isOpen: boolean;
    onClose: () => void;
    errors: MediaError[] | undefined;
    storeName?: string;
}

export default function MediaErrorsModal({ isOpen, onClose, errors, storeName }: MediaErrorsModalProps) {
    if (!isOpen) return null;

    return (
        <Modal closeModal={onClose} actionWhenCloseTouchOutside={onClose} blur={false}>
            <div className="bg-white dark:bg-[#20200e] text-[#1c1c0d] dark:text-white flex flex-col max-h-[90vh] w-full max-w-2xl mx-auto rounded-lg overflow-hidden shadow-xl">

                {/* Header */}
                <header className="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-red-50/50 dark:bg-red-900/10">
                    <div className="flex items-center gap-3">
                        <div className="size-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center text-red-600 dark:text-red-400">
                            <span className="material-symbols-outlined text-lg">error_outline</span>
                        </div>
                        <div>
                            <h2 className="text-lg font-bold text-red-700 dark:text-red-400">Errores de Media</h2>
                            <p className="text-xs text-gray-500">{storeName || 'Tienda'}</p>
                        </div>
                    </div>
                    <button onClick={onClose} className="size-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 flex items-center justify-center transition-colors">
                        <span className="material-symbols-outlined text-sm">close</span>
                    </button>
                </header>

                {/* Content */}
                <div className="p-0 overflow-y-auto">
                    {!errors || errors.length === 0 ? (
                        <div className="p-10 flex flex-col items-center justify-center text-center text-gray-400">
                            <span className="material-symbols-outlined text-4xl mb-2 text-green-500">check_circle</span>
                            <p>No se encontraron errores de media registrados.</p>
                        </div>
                    ) : (
                        <table className="w-full text-sm text-left">
                            <thead className="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-800/50 sticky top-0">
                                <tr>
                                    <th scope="col" className="px-6 py-3">Archivo</th>
                                    <th scope="col" className="px-6 py-3">Mensaje de Error</th>
                                    <th scope="col" className="px-6 py-3">Fecha</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100 dark:divide-gray-800">
                                {errors.map((error) => (
                                    <tr key={error.id} className="bg-white dark:bg-[#20200e] hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                        <td className="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                            {error.file_name || 'Desconocido'}
                                        </td>
                                        <td className="px-6 py-4 text-red-600 dark:text-red-400 max-w-xs truncate" title={error.error_message}>
                                            {error.error_message}
                                        </td>
                                        <td className="px-6 py-4 text-gray-500 whitespace-nowrap">
                                            {new Date(error.created_at).toLocaleString()}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>

                {/* Footer */}
                <div className="p-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-[#1a1a0c] flex justify-end">
                    <button
                        onClick={onClose}
                        className="px-6 py-2 bg-white border border-gray-300 hover:bg-gray-50 dark:bg-transparent dark:border-gray-700 dark:hover:bg-gray-800 rounded-full font-medium text-sm transition-colors"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </Modal>
    );
}
