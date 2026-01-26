import { cancel } from "@/routes/campaign";
import { router } from "@inertiajs/react";
import Modal from "../Modal";
import { Button } from "../ui/button";
import { useEffect, useState } from "react";
import ms from "ms";

export default function CancelCampaignModal({ isOpen, closeModal, campaignId, setCampaignId }: { isOpen: boolean; closeModal: () => void; campaignId: string | null; setCampaignId: React.Dispatch<React.SetStateAction<string | null>> }) {

    const [count, SetCount] = useState(5);

    useEffect(() => {
        if (!isOpen) return;
        const timeoutId = setTimeout(() => SetCount(5), 0);
        const intervalId = setInterval(() => {
            SetCount(prev => {
                if (prev <= 1) {
                    clearInterval(intervalId);
                    return 0;
                }
                return prev - 1;
            });
        }, ms("1s"));
        return () => {
            clearTimeout(timeoutId);
            clearInterval(intervalId);
        };
    }, [isOpen]);

    if (!isOpen) return null
    return (
        <Modal className='w-90 bg-white p-6 ' closeModal={closeModal}>
            <h2 className="text-lg font-semibold mb-4">Confirmar cancelación de campaña</h2>
            <p className="mb-6">¿Estás seguro de que deseas cancelar esta campaña? Esta acción no se puede deshacer.</p>
            <div className="flex justify-end w- gap-4">
                <Button className='bg-locatel-oscuro text-white hover:bg-green-800' onClick={closeModal}>Cancelar</Button>
                <Button
                    disabled={count > 0}
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
                    {count > 0 ? ` (${count})` : ''}
                </Button>
            </div>
        </Modal>
    )
}