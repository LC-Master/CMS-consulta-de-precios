import { router } from "@inertiajs/react";
import Modal from "../Modal";
import { Button } from "../ui/button";

interface UserActionModalProps {
    isOpen: boolean;
    closeModal: () => void;
    userId: string;
    actionType: 'delete' | 'restore' | null;
    setUserId: (id: string) => void;
}

export default function UserActionModal({ isOpen, closeModal, userId, actionType, setUserId }: UserActionModalProps) {
    if (!isOpen || !actionType) return null;

    const isDelete = actionType === 'delete';

    // Configuración dinámica basada en la acción
    const config = {
        title: isDelete ? 'Confirmar desactivación' : 'Confirmar restauración',
        message: isDelete 
            ? '¿Estás seguro de que deseas desactivar este usuario? Perderá acceso al sistema inmediatamente.' 
            : '¿Deseas restaurar este usuario? Recuperará el acceso al sistema.',
        buttonText: isDelete ? 'Desactivar usuario' : 'Restaurar usuario',
        buttonColor: isDelete ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700',
        method: isDelete ? 'delete' : 'put' as 'delete' | 'put', // Casting for TS
        route: isDelete ? `/user/${userId}` : `/users/${userId}/restore`
    };

    const handleAction = () => {
        // Ejecutamos la acción dinámica
        if (config.method === 'delete') {
            router.delete(config.route, {
                preserveScroll: true,
                onSuccess: () => {
                    closeModal();
                    setUserId('');
                }
            });
        } else {
            router.put(config.route, {}, {
                preserveScroll: true,
                onSuccess: () => {
                    closeModal();
                    setUserId('');
                }
            });
        }
    };

    return (
        <Modal className='w-full max-w-lg bg-white p-6 rounded-lg shadow-xl' closeModal={closeModal}>
            <div className="text-center sm:text-left">
                <h2 className={`text-lg font-semibold mb-2 ${isDelete ? 'text-red-600' : 'text-green-600'}`}>
                    {config.title}
                </h2>
                <p className="text-gray-600 mb-6 text-sm">
                    {config.message}
                </p>
                
                <div className="flex justify-end gap-3 mt-6">
                    <Button 
                        variant="secondary" // Asumiendo que tienes variantes, si no, usa className gris
                        className="bg-gray-100 text-gray-700 hover:bg-gray-200"
                        onClick={closeModal}
                    >
                        Cancelar
                    </Button>
                    
                    <Button
                        className={`text-white ${config.buttonColor}`}
                        onClick={handleAction}
                    >
                        {config.buttonText}
                    </Button>
                </div>
            </div>
        </Modal>
    );
}