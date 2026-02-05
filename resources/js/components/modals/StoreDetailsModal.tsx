import React from 'react';
import Modal from '../Modal';
import { StoreDetailsModalProps } from '@/types/store/index.type';
import { useForm } from '@inertiajs/react';
import { X, Link as LinkIcon, Store as StoreIcon, HardDrive, Clock, Activity, Calendar } from 'lucide-react';
import { update } from '@/routes/stores/sync/url';
import InputError from '../input-error';
import SyncStatusPill from '../SyncStatusPill';
import { Button } from '../ui/button';
import { Input } from '../ui/input';


export default function StoreDetailsModal({ isOpen, onClose, store }: StoreDetailsModalProps) {
    const defaultSyncState = store?.sync_state;

    const { data, setData, post, processing, errors } = useForm({
        url: defaultSyncState?.url || '',
    });

    React.useEffect(() => {
        if (store) {
            setData('url', store.sync_state?.url || '');
        }

    }, [store, setData]);

    if (!isOpen || !store) return null;

    const formatDate = (dateString?: string) => {
        if (!dateString) return 'No disponible';
        return new Date(dateString).toLocaleString('es-VE', {
            day: 'numeric',
            month: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    };

    const formatBytes = (bytes: number, decimals = 2) => {
        if (!+bytes) return '0 Bytes';

        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
    };

    const getUptimeDiff = (dateString?: string) => {
        if (!dateString) return 'No disponible';
        
        const start = new Date(dateString).getTime();
        const now = new Date().getTime();
        
        let diff = Math.abs(now - start);
        
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        diff -= days * (1000 * 60 * 60 * 24);
        
        const hours = Math.floor(diff / (1000 * 60 * 60));
        diff -= hours * (1000 * 60 * 60);
        
        const minutes = Math.floor(diff / (1000 * 60));
        
        const parts = [];
        if (days > 0) parts.push(`${days}d`);
        if (hours > 0) parts.push(`${hours}h`);
        parts.push(`${minutes}m`);
        
        return parts.join(' ');
    };

    const syncState = store.sync_state;

    const handleUpdateUrl = (e: React.FormEvent) => {
        e.preventDefault();
        post(update({ ID: Number(store.id) }).url, {
            preserveScroll: true,
            onSuccess: () => {
                onClose();
            }
        });
    };

    return (
        <Modal
            closeModal={onClose}
            actionWhenCloseTouchOutside={onClose}
            className="p-0 bg-[#f8f9fa] overflow-hidden max-w-2xl w-full rounded-xl"
            blur={true}
        >
            {/* Header Green */}
            <div className="bg-[#10b981] px-6 py-4 flex items-center justify-between text-white">
                <div className="flex items-center gap-4">
                    <div className="p-2 bg-white/20 rounded-full">
                        <StoreIcon className="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <h2 className="text-lg font-bold">Detalles de la Tienda</h2>
                        <p className="text-[10px] uppercase tracking-wide opacity-90">INFORMACIÓN Y SINCRONIZACIÓN</p>
                    </div>
                </div>
                <Button
                    onClick={onClose}
                    className="p-1 hovered:bg-white/10 rounded-full transition-colors bg-white/20 hover:bg-white/30"
                >
                    <X className="w-5 h-5" />
                </Button>
            </div>

            <div className="p-6 max-h-[80vh] overflow-y-auto bg-[#f8f9fa]">

                {/* General Info Section */}
                <div className="flex gap-2 items-center text-[#10b981] mb-3">
                    <span className="material-symbols-outlined rounded-full bg-[#10b981] text-white text-[10px] p-0.5 w-4 h-4 flex items-center justify-center font-bold">i</span>
                    <h3 className="font-bold text-xs tracking-widest text-[#9ca3af] uppercase">INFORMACIÓN GENERAL</h3>
                </div>

                <div className="bg-white rounded-2xl p-6 mb-6 shadow-sm">
                    <div className="grid grid-cols-2 gap-y-6 gap-x-8">
                        <div>
                            <p className="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">NOMBRE DE LA TIENDA</p>
                            <p className="text-sm font-bold text-gray-900">{store.name}</p>
                        </div>
                        <div>
                            <p className="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">CÓDIGO DE SUCURSAL</p>
                            <p className="text-sm font-bold text-gray-900">{store.store_code}</p>
                        </div>
                        <div>
                            <p className="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">REGIÓN / RAZÓN SOCIAL</p>
                            <p className="text-sm font-bold text-gray-900">{store.region}</p> {/* Assuming simple region for now */}
                        </div>
                        <div>
                            <p className="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">ID DE SISTEMA</p>
                            <p className="text-sm font-bold text-gray-900">{store.id}</p>
                        </div>
                    </div>
                </div>


                {/* Sync Status Section */}
                <div className="flex justify-between items-center mb-3">
                    <div className="flex gap-2 items-center">
                        <Activity className="w-4 h-4 text-[#10b981]" />
                        <h3 className="font-bold text-xs tracking-widest text-[#9ca3af] uppercase">ESTADO DE SINCRONIZACIÓN</h3>
                    </div>
                    {/* Status Pill */}
                    <SyncStatusPill status={syncState?.sync_status} className="px-3 py-1 text-[10px]" />
                </div>

                <div className="bg-white rounded-2xl p-6 shadow-sm">
                    {/* Endpoint URL */}
                    <div className="mb-6">
                        <label className="text-xs font-bold text-gray-700 mb-2 block">Endpoint de Sincronización (URL)</label>
                        <form onSubmit={handleUpdateUrl} className="flex gap-3">
                            <div className="relative flex-1 group">
                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <LinkIcon className="h-3.5 w-3.5 text-gray-400" />
                                </div>
                                <Input
                                    type="url"
                                    value={data.url}
                                    onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('url', e.target.value)}
                                    className="block w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-full leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-xs text-gray-600 font-medium transition-shadow hover:shadow-sm"
                                    placeholder="https://"
                                />
                            </div>
                            <Button
                                type="submit"
                                disabled={processing}
                                className="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-full transition-colors shadow-blue-200 shadow-md"
                            >
                                {processing ? '...' : 'Actualizar'}
                            </Button>
                        </form>
                        <InputError message={errors.url} className="mt-2" />
                    </div>

                    {/* Stats Cards */}
                    {syncState ? (
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div className="border border-gray-100 rounded-2xl p-3 bg-gray-50/50">
                                <p className="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">ÚLTIMA SYNC</p>
                                <div className="flex items-center gap-2 text-gray-700 font-bold text-xs">
                                    <Calendar className="w-3.5 h-3.5 text-[#10b981]" />
                                    <span>{formatDate(syncState.last_synced_at)}</span>
                                </div>
                            </div>

                            <div className="border border-gray-100 rounded-2xl p-3 bg-gray-50/50">
                                <p className="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">UPTIME</p>
                                <div className="flex items-center gap-2 text-gray-500 font-medium text-xs italic">
                                    <Clock className="w-3.5 h-3.5" />
                                    <span>{getUptimeDiff(syncState.uptimed_at)}</span>
                                </div>
                            </div>

                            <div className="border border-gray-100 rounded-2xl p-3 bg-gray-50/50">
                                <p className="text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1">ESPACIO EN DISCO</p>
                                {syncState.disk ? (
                                    <>
                                        <div className="flex flex-col gap-1 text-gray-700 text-xs">
                                            <div className="flex justify-between">
                                                <span className="text-gray-400 text-[10px] uppercase">Libre</span>
                                                <span className="font-bold text-green-600">{formatBytes(syncState.disk.free)}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-gray-400 text-[10px] uppercase">Usado</span>
                                                <span className="font-bold text-blue-600">{formatBytes(syncState.disk.used)}</span>
                                            </div>
                                            <div className="flex justify-between border-t border-dashed border-gray-200 pt-1 mt-1">
                                                <span className="text-gray-400 text-[10px] uppercase">Total</span>
                                                <span className="font-bold">{formatBytes(syncState.disk.size)}</span>
                                            </div>
                                        </div>
                                    </>
                                ) : (
                                    <div className="flex items-center gap-2 text-gray-500 font-medium text-xs italic">
                                        <HardDrive className="w-3.5 h-3.5" />
                                        <span>No disponible</span>
                                    </div>
                                )}
                            </div>
                        </div>
                    ) : (
                        <div className="text-center py-6 text-gray-400 italic bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            Sin información de sincronización
                        </div>
                    )}
                </div>

            </div>

            {/* Footer */}
            <div className="bg-white px-8 py-5 flex justify-end items-center gap-4 border-t border-gray-100">
                <Button
                    onClick={onClose}
                    className="text-gray-500 bg-amber-50 hover:bg-amber-300 font-bold text-sm hover:text-gray-800 transition-colors"
                >
                    Cerrar Detalles
                </Button>
            </div>
        </Modal>
    );
}
