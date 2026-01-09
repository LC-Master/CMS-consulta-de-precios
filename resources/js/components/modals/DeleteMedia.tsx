import { destroy } from "@/routes/media";
import { router } from "@inertiajs/react";
import Modal from "../Modal";
import { Button } from "../ui/button";

export default function DeleteMedia({isOpen, closeModal, mediaId, setMediaId }: { isOpen: boolean; closeModal: () => void; mediaId: string; setMediaId: (id: string) => void }) {
    if (!isOpen) return null;

    return (<Modal className='w-90 bg-white p-6 ' closeModal={closeModal}>
        <h2 className="text-lg font-semibold mb-4">Confirmar eliminación de media</h2>
        <p className="mb-6">¿Estás seguro de que deseas eliminar este media? Esta acción no se puede deshacer.</p>
        <div className="flex justify-end w- gap-4">
            <Button className='bg-locatel-oscuro text-white hover:bg-green-800' onClick={closeModal}>
                Cancelar
            </Button>
            <Button
                className="bg-red-600 text-white hover:bg-red-700"
                onClick={() => {
                    router.delete(destroy({ id: mediaId }).url, {
                        reset: ['medias', 'flash'],
                        only: ['medias', 'flash'],
                        preserveScroll: true,
                        onSuccess: () => {
                            closeModal();
                            setMediaId('');
                        }
                    });
                }}
            >
                Eliminar media
            </Button>
        </div>
    </Modal>)
}