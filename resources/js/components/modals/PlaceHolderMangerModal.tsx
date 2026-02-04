import React, { useState, useRef, useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import Modal from '../Modal';
import { Store } from '@/types/store/index.type';
import { cdn } from '@/routes/media';
import { update } from '@/routes/stores/placeholder'
import { X, Info } from 'lucide-react';
import { Button } from "../ui/button";

export default function PlaceHolderMangerModal({ isOpen, store, onClose }: { isOpen: boolean; store: Store | null; onClose: () => void }) {
    const fileInputRef = useRef<HTMLInputElement>(null);
    const [previewUrl, setPreviewUrl] = useState<string | null>(store?.sync_state?.placeholder?.id ? cdn({ id: store.sync_state.placeholder.id }).url : null);

    const { data, setData, post, processing, reset, errors } = useForm({
        store_id: store?.id,
        file: null as File | null,
    });

    useEffect(() => {
        if (store?.id) setData('store_id', store.id);
    }, [setData, store]);

    useEffect(() => {
        (() => {
            if (isOpen) {
                if (store?.sync_state?.placeholder?.id) {
                    setPreviewUrl(cdn({ id: store.sync_state.placeholder.id }).url);
                } else {
                    setPreviewUrl(null);
                }
            } else {
                setPreviewUrl(null);
                reset();
            }
        })()
    }, [isOpen, store, reset])


    if (!isOpen) return null;

    const handleFileSelect = async (e: React.ChangeEvent<HTMLInputElement>) => {
        const selectedFile = e.target.files?.[0];
        if (selectedFile) {
            setData('file', selectedFile);
            setPreviewUrl(URL.createObjectURL(selectedFile));
        }
    };

    const saveChanges = (e: React.FormEvent) => {
        e.preventDefault();

        post(update({ ID: Number(store?.id) }).url, {
            forceFormData: true,
            onSuccess: () => {
                reset();
                onClose();
            },
        });
    };

    return (
        <Modal closeModal={onClose} actionWhenCloseTouchOutside={onClose} className="w-96 bg-white p-6 rounded-lg" blur={false}>
            {/* Header */}
            <header className="flex items-start justify-end px-6 border-b border-transparent">
                <Button onClick={onClose} className="size-6 rounded-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 flex items-center justify-center transition-colors">
                    <X className="w-3 h-3 text-gray-600 dark:text-gray-300" />
                </Button>
            </header>

            <div className="flex flex-col lg:flex-row flex-1 overflow-hidden">
                {/* Left: Preview */}
                <div className="flex-1 p-6 pt-2 flex flex-col">
                    <div className="mb-4">
                        <h3 className="text-xl font-bold mb-1">Edición del PlaceHolder</h3>
                        <p className="text-gray-500 dark:text-gray-400 text-xs">Pre visualización de la imagen o video</p>
                    </div>

                    {/* Preview Box */}
                    <div className="relative w-full aspect-video bg-black rounded-2xl overflow-hidden group shadow-inner ring-1 ring-black/5">
                        {previewUrl ? (
                            data.file?.type.includes('video') || store?.sync_state?.placeholder?.mime_type?.includes('video') ? (
                                <video src={previewUrl} controls className="w-full h-full object-cover" />
                            ) : (
                                <img src={previewUrl} className="w-full h-full object-cover" />
                            )
                        ) : (
                            <div className="absolute inset-0 flex items-center justify-center text-white/50">
                                Archivo no seleccionado
                            </div>
                        )}
                    </div>

                    {/* Info Footer */}
                    <div className="flex items-center gap-2 mt-3 text-xs text-gray-500 dark:text-gray-400">
                        <span className="material-symbols-outlined text-base">
                            <Info />
                        </span>
                        <div className="flex gap-1">
                            <span>Archivo seleccionado:</span>
                            <span className="font-bold text-black dark:text-white">
                                {data.file?.name || 'No seleccionado'}
                            </span>
                        </div>
                    </div>
                </div>

                {/* Right: Upload Controls */}
                <div className="w-full lg:w-80 p-6 pt-8 border-l border-gray-100 dark:border-gray-800 bg-white dark:bg-[#20200e]">
                    <h3 className="text-base font-bold mb-3">Sube una nueva versión</h3>

                    {/* Upload Dropzone */}
                    <div
                        onClick={() => fileInputRef.current?.click()}
                        className="w-full aspect-square max-h-60 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl flex flex-col items-center justify-center gap-3 cursor-pointer hover:border-yellow-400 transition-colors bg-gray-50/50 dark:bg-transparent group"
                    >
                        <div className="size-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-full flex items-center justify-center text-yellow-500 mb-1 group-hover:scale-110 transition-transform">
                            <span className="material-symbols-outlined text-2xl">Subir Media</span>
                        </div>
                        <div className="text-center space-y-1">
                            <p className="font-bold text-sm">Arrastra y suelta el archivo aquí</p>
                            <p className="text-[10px] text-gray-400 font-medium">Tamaño máximo de archivo: 150MB</p>
                        </div>

                        <Button className="px-4 py-2 bg-[#f3f3e8] text-gray-800 dark:bg-gray-800 rounded-full font-bold text-xs mt-2 hover:bg-[#eaeada] transition-colors">
                            Buscar archivo
                        </Button>
                        <input
                            type="file"
                            ref={fileInputRef}
                            className="hidden"
                            onChange={handleFileSelect}
                            accept=".jpg,.png,.webp,.jpeg,.mp4"
                        />
                    </div>

                    {/* Supported Formats */}
                    <div className="mt-6 p-4 bg-[#f8f8f4] dark:bg-gray-800/50 rounded-2xl">
                        <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-2">Formatos soportados</p>
                        <div className="flex gap-1">
                            {['JPG', 'PNG', 'WEBP', 'JPEG', 'MP4'].map(fmt => (
                                <span key={fmt} className="px-3 py-1 bg-white dark:bg-black/20 border border-gray-200 dark:border-gray-700 rounded-full text-[9px] font-bold shadow-sm">{fmt}</span>
                            ))}
                        </div>
                        {errors.file && <p className="text-red-500 text-xs mt-2 font-medium">{errors.file}</p>}
                    </div>

                    {/* Actions */}
                    <div className="mt-auto pt-4 flex flex-col gap-2">
                        <Button
                            onClick={saveChanges}
                            disabled={!data.file || processing}
                            className={`w-full py-3 rounded-full h-12 font-bold text-sm shadow-sm transition-all text-black
                                    ${!data.file || processing
                                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                                    : 'bg-locatel-medio text-white hover:bg-locatel-claro'
                                }`}
                        >
                            {processing ? 'Uploading...' : 'Save Changes'}
                        </Button>
                        <Button
                            onClick={onClose}
                            className="w-full py-2 font-bold text-xs bg-white hover:bg-gray-100 text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white transition-colors"
                        >
                            Cancel
                        </Button>
                    </div>
                </div>
            </div>
        </Modal>
    );
}