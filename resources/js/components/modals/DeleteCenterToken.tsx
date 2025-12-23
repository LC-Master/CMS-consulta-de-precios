import Modal from "@/components/Modal";
import { useState, useEffect } from "react";
import { useForm, router } from "@inertiajs/react";
import { Button } from "../ui/button";
import { destroy } from "@/routes/centertokens";

export default function DeleteCenterToken({ closeModal, tokenId }: { closeModal: () => void; tokenId: number }) {
    const [counter, setCounter] = useState(5);

    useEffect(() => {
        if (counter === 0) return;
        const timer = setTimeout(() => setCounter(counter - 1), 1000);
        return () => clearTimeout(timer);
    }, [counter]);
    const { delete: deleteToken } = useForm("delete")

    return (
        <Modal blur={false} className="bg-white" closeModal={closeModal}>
            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    deleteToken(destroy({ centertoken: tokenId }).url, {
                        onSuccess: () => {
                            router.reload({ only: ['centerTokens', 'flash'] });
                            closeModal();
                        },
                        preserveScroll: true
                    });
                }}
                className="p-6"
            >
                <h2 className="text-xl font-bold mb-4">Confirmar eliminación</h2>
                <p className="mb-6">¿Estás seguro de que deseas eliminar este token? Esta acción no se puede deshacer.</p>
                <p className="text-red-500 wrap-break-word mb-6">
                    Nota: Al borrar el token, cualquier aplicación o servicio que dependa de él para la autenticación perderá el acceso inmediatamente, lo que significa que las tiendas dejarán de sincronizar.
                </p>
                <div className="flex justify-end gap-4">
                    <Button
                        type='button'
                        onClick={closeModal}
                        className="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded"
                    >
                        Cancelar
                    </Button>
                    <Button
                        type="submit"
                        disabled={counter > 0}
                        className="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded"
                    >
                        Eliminar {counter > 0 ? `(${counter})` : ''}
                    </Button>
                </div>
            </form>
        </Modal>
    )

}