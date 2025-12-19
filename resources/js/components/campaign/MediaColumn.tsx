import { MediaColumnProps } from "@/types/campaign/media.types";
import { Label } from "@radix-ui/react-dropdown-menu";
import { Button } from "../ui/button";
import MediaItemCard from "./MediaItemCard";

export default function MediaColumn({ title, items, onMoveToOther, onMoveUp, onMoveDown, onRemove, errors }: MediaColumnProps) {
    return (
        <div className='w-1/2'>
            <Label>{title}</Label>
            <div className="h-60 max-h-60 overflow-y-auto border border-gray-300 rounded p-2">
                {items.map((item) => (
                    <MediaItemCard
                        key={item.id}
                        item={item}
                        controls={
                            <>
                                <Button type="button" onClick={(e) => { e.preventDefault(); onMoveToOther(item) }}>
                                    {title === 'AM' ? 'PM' : 'AM'}
                                </Button>
                                <Button type="button" onClick={(e) => { e.preventDefault(); onMoveUp(item.id) }}>↑</Button>
                                <Button type="button" onClick={(e) => { e.preventDefault(); onMoveDown(item.id) }}>↓</Button>
                                <Button type="button" onClick={(e) => { e.preventDefault(); onRemove(item) }}>Eliminar</Button>
                            </>
                        }
                    />
                ))}
            </div>
            {errors && <div className="text-red-500 text-sm mt-1">{errors}</div>}
        </div>
    )
}
