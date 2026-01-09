import { useState, useEffect } from "react" 

export default function useModal(initialState: boolean = false) {
    const [isOpen, setIsOpen] = useState(initialState)

    const closeModal = (): void => {
        setIsOpen(false)
    }
    const openModal = (): void => {
        setIsOpen(true)
    }

    useEffect(() => {
        if (isOpen) {
            document.body.style.overflow = 'hidden';
            
        } else {
            document.body.style.overflow = 'unset';
        }

        return () => {
            document.body.style.overflow = 'unset';
        };
    }, [isOpen]);

    return {
        isOpen,
        openModal,
        closeModal,
    }
}