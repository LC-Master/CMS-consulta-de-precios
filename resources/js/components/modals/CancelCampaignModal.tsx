import { cancel } from "@/routes/campaign";
import { router } from "@inertiajs/react";
import Modal from "../Modal";
import { Button } from "../ui/button";

export default function CancelCampaignModal({ isOpen, closeModal, campaignId, setCampaignId }: { isOpen: boolean; closeModal: () => void; campaignId: string | null; setCampaignId: React.Dispatch<React.SetStateAction<string | null>> }) {
    if (!isOpen) return null
    return (
        <Modal className='w-90 bg-white p-6 ' closeModal={closeModal}>
            <h2 className="text-lg font-semibold mb-4">Confirmar cancelación de campaña</h2>
            <p className="mb-6">¿Estás seguro de que deseas cancelar esta campaña? Esta acción no se puede deshacer.</p>
            <div className="flex justify-end w- gap-4">
                <Button className='bg-locatel-oscuro text-white hover:bg-green-800' onClick={closeModal}>Cancelar</Button>
                <Button
                    className="bg-red-600 text-white hover:bg-red-700"
                    onClick={() => {
                        router.visit(cancel({ id: campaignId! }).url, {
                            method: 'get',
                            preserveState: false,
                            preserveScroll: true,
                            onSuccess: () => {
                                closeModal();
                                setCampaignId(null);
                            },
                        });
                    }}
                >
                    Cancelar campaña
                </Button>
            </div>
        </Modal>
    )
}