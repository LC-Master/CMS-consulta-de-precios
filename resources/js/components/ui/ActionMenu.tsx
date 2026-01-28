import { Link } from "@inertiajs/react";
import { EllipsisVertical } from "lucide-react";
import { ReactNode, useState, useRef, useEffect } from "react";
import { createPortal } from "react-dom";

interface ActionMenuProps {
    children: ReactNode;
}

interface ItemProps {
    children: ReactNode;
    onClick?: () => void;
    className?: string;
    href?: string;
    variant?: 'default' | 'danger';
}

const Item = ({ children, onClick, className = "", variant = 'default' }: ItemProps) => {
    const variants = {
        default: "text-gray-700 hover:bg-gray-50 active:bg-gray-100",
        danger: "text-red-600 hover:bg-red-50 active:bg-red-100"
    };
    const handleAction = (e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();

        if (onClick) {
            onClick();
        }
    };
    return (
        <button
            type="button"
            onMouseDown={handleAction}
            className={`flex items-center gap-3 px-4 py-2.5 text-sm transition-colors cursor-pointer w-full text-left ${variants[variant]} ${className}`}
        >
            {children}
        </button>
    );
};

const ItemLink = ({ children, href, className = "", variant = 'default' }: ItemProps) => {
    const variants = {
        default: "text-gray-700 hover:bg-gray-50 active:bg-gray-100",
        danger: "text-red-600 hover:bg-red-50 active:bg-red-100"
    };

    return (
        <Link
            type="button"
            onClick={(e) => {
                e.stopPropagation();
            }}
            href={href}
            viewTransition
            className={`flex items-center gap-3 px-4 py-2.5 text-sm transition-colors cursor-pointer w-full text-left ${variants[variant]} ${className}`}
        >
            {children}
        </Link>
    );
};

const Separator = () => <div className="h-px bg-gray-100 my-1" />;

export function ActionMenu({ children }: ActionMenuProps) {
    const [isOpen, setIsOpen] = useState(false);
    const [coords, setCoords] = useState({ top: 0, left: 0 });
    const triggerRef = useRef<HTMLButtonElement>(null);

    const handleToggle = (e: React.MouseEvent) => {
        e.stopPropagation();

        if (!isOpen && triggerRef.current) {
            const rect = triggerRef.current.getBoundingClientRect();
            const MENU_APPROX_HEIGHT = 150;
            const spaceBelow = window.innerHeight - rect.bottom;

            if (spaceBelow < 400) {
                const topAbove = window.scrollY + rect.top - 8 - MENU_APPROX_HEIGHT;
                setCoords({
                    top: Math.max(topAbove, window.scrollY + 8),
                    left: rect.right + window.scrollX - 224,
                });
            } else {
                setCoords({
                    top: rect.bottom + window.scrollY + 8,
                    left: rect.right + window.scrollX - 224,
                });
            }
        }
        setIsOpen(!isOpen);
    };

    useEffect(() => {
        const closeMenu = () => setIsOpen(false);

        if (isOpen) {
            window.addEventListener('click', closeMenu, true);
            window.addEventListener('scroll', closeMenu, true);
        }

        return () => {
            window.removeEventListener('click', closeMenu, true);
            window.removeEventListener('scroll', closeMenu, true);
        };
    }, [isOpen]);

    return (
        <div className="relative inline-block text-left">
            <button
                ref={triggerRef}
                type="button"
                onClick={handleToggle}
                className={`p-1.5 rounded-full transition-all duration-200 ${isOpen ? 'bg-gray-200 text-gray-900 shadow-inner' : 'text-gray-400 hover:bg-gray-100'
                    }`}
            >
                <EllipsisVertical className="w-5 h-5" />
            </button>

            {isOpen && createPortal(
                <div
                    style={{ position: 'absolute', top: coords.top, left: coords.left, minWidth: '14rem' }}
                    className="bg-white rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.3)] border border-gray-200 py-1.5 z-9999 animate-in fade-in zoom-in duration-100"
                    onClick={(e) => e.stopPropagation()}
                >
                    {children}
                </div>,
                document.body
            )}
        </div>
    );
}

ActionMenu.Item = Item;
ActionMenu.ItemLink = ItemLink;
ActionMenu.Separator = Separator;