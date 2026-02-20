import { SyncStatusEnum } from '@/enums/SyncStatusEnum';
import { useEffect } from 'react';
import { toast, ToastContainer } from 'react-toastify';

export default function useToastSync(flash: { status: SyncStatusEnum; message: string } | undefined) {
    const containerId = 'sync-toast-container';

    useEffect(() => {
        if (!flash) return;
        
        const options = {
            position: 'top-right' as const,
            autoClose: 5000,
            hideProgressBar: false,
            closeOnClick: true,
            pauseOnHover: true,
            draggable: true,
            progress: undefined,
            theme: 'light' as const,
            containerId
        };

        if (flash.status === SyncStatusEnum.SUCCESS) {
            toast.success(flash.message, options);
        } else if (flash.status === SyncStatusEnum.FAILED) {
            toast.error(flash.message, options);
        } else {
            toast.info(flash.message, options);
        }
    }, [flash]);

    const ToastComponent = (
        <ToastContainer 
            containerId={containerId} 
            style={{ zIndex: 100000, marginTop: '4rem' }} 
        />
    );
    return { ToastContainer: () => ToastComponent };
}
