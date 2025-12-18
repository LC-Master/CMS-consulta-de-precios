import { useEffect } from 'react';
import { toast, ToastContainer } from 'react-toastify';

export default function useToast(flash: { success?: string; error?: string } | undefined) {
    useEffect(() => {
        if (flash?.success) {
            toast.success(flash.success, {
                position: 'top-right',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined,
                theme: 'light',
            });
        }
        if (flash?.error) {
            toast.error(flash.error, {
                position: 'top-right',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined,
                theme: 'light',
            });
        }
    }, [flash]);

    const ToastComponent = (
        <ToastContainer />
    );
    return { ToastContainer: () => ToastComponent };
}
