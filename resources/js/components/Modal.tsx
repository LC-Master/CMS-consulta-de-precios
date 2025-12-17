export default function Modal({ children, closeModal }: { children: React.ReactNode; closeModal: () => void; }) {
    return (
        <div
            className="fixed inset-0 flex items-center backdrop-blur-sm justify-center z-50"
            onClick={closeModal}
        >

            <div
                className="bg-white rounded-lg shadow-2xl w-11/12 md:w-2/3 lg:w-1/2 xl:w-2/3 p-6 relative z-50"
                onClick={(e) => e.stopPropagation()}
            >

                {children}
            </div>
        </div>
    );
}