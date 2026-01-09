import { destroy } from "@/routes/campaign";
import { router } from "@inertiajs/react";
import { Trash } from "lucide-react";
import Modal from "../Modal";
import { Button } from "../ui/button";
import { useEffect, useState } from "react";

export default function DeleteCampaignModal({ isOpen, campaignId, closeDeleteModal }: {
    isOpen: boolean,
    campaignId: string | null,
    closeDeleteModal: () => void
}) {
    const [count, setCount] = useState(5)
    useEffect(() => {
        if (count === 0) return;
        const timer = setTimeout(() => setCount(count - 1), 1000);
        return () => clearTimeout(timer);
    }, [count]);
    if (!isOpen) return null
    return (
        <Modal className="w-96 bg-white p-6 rounded-lg" closeModal={closeDeleteModal}>
            <div className="flex items-start gap-4">
                <div className="shrink-0">
                    <div className="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <Trash className="w-6 h-6 text-red-600" />
                    </div>
                </div>
                <div>
                    <h2 className="text-lg font-semibold mb-1">Eliminar campaña</h2>
                    <p className="text-sm text-gray-600 mb-2">¿Estás seguro de que deseas eliminar esta campaña? Esta acción es irreversible y no podrá deshacerse desde la interfaz.</p>
                    <p className="text-xs text-gray-500">La campaña dejará de aparecer en las listas activas, pero permanecerá en el histórico para auditoría.</p>
                </div>
            </div>
            <div className="mt-6 flex justify-end gap-3">
                <Button variant="outline" onClick={closeDeleteModal}>Cancelar</Button>
                <Button className="bg-red-600 text-white hover:bg-red-700" disabled={count !== 0} onClick={() => {
                    router.delete(destroy({ id: campaignId! }).url, {
                        only: ['campaigns', 'flash'],
                        reset: ['campaigns', 'flash'],
                        preserveScroll: true
                    });
                    closeDeleteModal();
                }}>Eliminar {count > 0 && `(${count})`}</Button>
            </div>
        </Modal>
    )
}