import { createPortal } from "react-dom";
import Modal from "@/components/Modal";
import useModal from "@/hooks/use-modal";
import {
    Button
} from "@/components/ui/button";

export default function TimeLineCreate() {
    const { isOpen, openModal, closeModal } = useModal(false)

    return (
        <div>
            {isOpen && createPortal(
                <Modal closeModal={closeModal}>
                    <div>Modal Content</div>
                </Modal>,
                document.body
            )}
         
            <Button onClick={openModal}>Open Modal</Button>
        </div>
    )
}