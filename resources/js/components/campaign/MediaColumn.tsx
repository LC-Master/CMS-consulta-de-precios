import { MediaColumnProps } from "@/types/campaign/media.types";
import { Label } from "@radix-ui/react-dropdown-menu";
import { Button } from "../ui/button";
import MediaItemCard from "./MediaItemCard";

export default function MediaColumn({ title, items, onMoveToOther, onMoveUp, onMoveDown, onRemove, errors }: MediaColumnProps) {
    const type = title === 'AM'
    const renderError = (err: string) => typeof err === 'string' ? err : JSON.stringify(err)

    return (
        <div className='w-1/2'>
            <div className="flex justify-between items-center mb-4 ml-1">
                <Label className="block text-sm font-bold  text-gray-700">BLOQUE {title}  {type ? '(06:00 - 12:00)' : '(12:00 - 22:00)'}</Label>
                <span className={`${items?.length ? 'bg-blue-200 text-blue-600' : 'bg-gray-200'} rounded px-2 py-1 text-sm`}>{items?.length ? `${items.length} items` : 'vacío'}</span>
            </div>
            <div className={`h-60 max-h-80 overflow-y-auto ${items.length ? '' : 'border-dashed border-red-500'} border border-gray-300 rounded p-2`}>
                {items.length === 0 && (
                    <div className="flex flex-col items-center justify-center h-50 text-center text-gray-500">
                        <div>No hay elementos multimedia en este bloque.</div>
                        {errors && <div className="text-red-500 text-sm mt-2 wrap-break-words">{renderError(errors)}</div>}
                    </div>
                )}
                {items.map((item) => (
                    <MediaItemCard
                        key={item.id}
                        item={item}
                        controls={
                            <>
                                <Button type="button" className="bg-locatel-medio hover:bg-locatel-oscuro" onClick={(e) => { e.preventDefault(); onMoveToOther(item) }}>
                                    {type ? 'PM' : 'AM'}
                                </Button>
                                <Button type="button" className="bg-locatel-medio hover:bg-locatel-oscuro" onClick={(e) => { e.preventDefault(); onMoveUp(item.id) }}>↑</Button>
                                <Button type="button" className="bg-locatel-medio hover:bg-locatel-oscuro" onClick={(e) => { e.preventDefault(); onMoveDown(item.id) }}>↓</Button>
                                <Button type="button" className="bg-locatel-medio hover:bg-locatel-oscuro" onClick={(e) => { e.preventDefault(); onRemove(item) }}>Eliminar</Button>
                            </>
                        }
                    />
                ))}
            </div>
            {items.length > 0 && errors && <div className="text-red-500 text-sm mt-1 wrap-break-words">{renderError(errors)}</div>}
        </div>
    )
}
