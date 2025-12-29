import { MediaListProps } from "@/types/campaign/media.types";
import { Label } from "../ui/label";
import { Button } from "../ui/button";
import { Input } from "../ui/input";
import MediaItemCard from "./MediaItemCard";

export default function MediaList({ value, onSearch, mediaList, onMoveToAm, onMoveToPm }: MediaListProps) {
    return (
        <div>
            <Label htmlFor="media-search">Contenido multimedia</Label>
            <Input
                id="media-search"
                name="media_search"
                type="search"
                value={value}
                placeholder="Buscar multimedia..."
                className="mb-4 mt-4"
                onChange={onSearch}
                aria-label="Buscar multimedia"
                aria-describedby="media-search-help"
                aria-controls="media"
                autoComplete="off"
            />
            <div id='media' className="min-h-40 max-h-80 overflow-y-auto border-2 bg-white border-gray-300 rounded-sm p-2">
                {mediaList.length !== 0 ? mediaList.map((item) => (
                    <MediaItemCard
                        key={item.id}
                        item={item}
                        controls={
                            <>
                                <Button
                                    type="button"
                                    className="px-3 py-1 text-sm bg-locatel-medio hover:bg-locatel-oscuro"
                                    onClick={(e) => { e.preventDefault(); onMoveToAm(item) }}
                                    aria-label={`Mover ${item.name} a AM`}
                                >
                                    AM
                                </Button>

                                <Button
                                    type="button"
                                    className="px-3 py-1 text-sm bg-locatel-medio hover:bg-locatel-oscuro"
                                    onClick={(e) => { e.preventDefault(); onMoveToPm(item) }}
                                    aria-label={`Mover ${item.name} a PM`}
                                >
                                    PM
                                </Button>
                            </>
                        }
                    />
                )) : <div className="flex items-center justify-center h-30 text-center text-gray-500">
                    No hay elementos multimedia disponibles.
                </div>}
            </div>
        </div>
    )
}
