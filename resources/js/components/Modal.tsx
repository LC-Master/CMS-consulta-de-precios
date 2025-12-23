export default function Modal({ children, closeModal, className, blur = true }: { blur?: boolean; children: React.ReactNode; closeModal: () => void; className?: string }) {
    return (
        <div
            className={`fixed inset-0 flex items-center ${blur ? 'backdrop-blur-md' : ''} justify-center z-50`}
            onClick={closeModal}
        >

            <div
                className={`${className ? className : 'bg-white w-11/12 md:w-2/3 lg:w-1/2 xl:w-2/3 p-6'}  rounded-lg shadow-2xl relative z-50`}
                onClick={(e) => e.stopPropagation()}
            >

                {children}
            </div>
        </div>
    );
}