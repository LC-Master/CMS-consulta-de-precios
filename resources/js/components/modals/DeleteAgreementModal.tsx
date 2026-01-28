import { destroy } from "@/routes/agreement";
import { router } from "@inertiajs/react";
import Modal from "../Modal";
import { Button } from "../ui/button";

export default function DeleteAgreementModal({ isOpen, closeModal, agreementId }: { isOpen: boolean; closeModal: () => void; agreementId: string }) {
    if (!isOpen) return null
    return (
        <Modal className='w-90 bg-white p-6 ' closeModal={closeModal}>
            <h2 className='text-xl font-semibold mb-4'>Confirmar eliminación</h2>
            <p className='mb-6'>¿Estás seguro de que deseas eliminar este acuerdo? Esta acción no se puede deshacer.</p>
            <div className='flex justify-end gap-4'>
                <Button variant='outline' onClick={closeModal}>Cancelar</Button>
                <Button className='bg-red-600 text-white' onClick={() => {
                    router.delete(destroy({ id: agreementId }).url, {
                        only: ['agreements', 'flash'],
                        reset: ['agreements', 'flash'],
                        preserveScroll: true
                    });
                    closeModal();
                }}>Eliminar</Button>
            </div>
        </Modal>
    )
}