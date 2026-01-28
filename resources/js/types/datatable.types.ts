import { ReactNode } from "react"

export type Column<T> = {
    key: string
    header: string
    className?: string
    render: (row: T) => ReactNode
}

export interface DataTableProps<T> {
    data: T[]
    columns: Column<T>[]
    emptyText?: string
    rowKey: (row: T) => string | number
}