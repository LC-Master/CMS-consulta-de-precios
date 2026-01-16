import { MediaItem } from "./index.types"

export type MediaColumnProps = {
    title: string
    items: MediaItem[]
    onMoveToOther: (item: MediaItem) => void
    onMoveUp: (id: MediaItem['id']) => void
    onMoveDown: (id: MediaItem['id']) => void
    onRemove: (item: MediaItem) => void
    errors?: string
}
export type MediaListProps = {
    mediaList: MediaItem[]
    onMoveToAm: (item: MediaItem) => void
    onMoveToPm: (item: MediaItem) => void
    value: string
    onSearch: (e: React.ChangeEvent<HTMLInputElement>) => void
}
