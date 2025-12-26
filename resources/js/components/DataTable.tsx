import { InfiniteScroll } from '@inertiajs/react'
import { ReactNode } from 'react'

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
    infiniteData: string
}

export function DataTable<T>({
    data,
    columns,
    rowKey,
    emptyText = 'No hay registros',
    infiniteData
}: DataTableProps<T>) {
    return (
        <div className="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <InfiniteScroll data={infiniteData}>
                <table className="min-w-full bg-white divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            {columns.map(col => (
                                <th
                                    key={col.key}
                                    className={`px-4 py-2 text-left text-sm font-medium text-gray-700 ${col.className ?? ''}`}
                                >
                                    {col.header}
                                </th>
                            ))}
                        </tr>
                    </thead>

                    <tbody className="bg-white divide-y divide-gray-100">
                        {data.length ? (
                            data.map(row => (
                                <tr key={rowKey(row)} className="hover:bg-gray-50">
                                    {columns.map(col => (
                                        <td
                                            key={col.key}
                                            className="px-4 py-3 text-sm text-gray-900"
                                        >
                                            {col.render(row)}
                                        </td>
                                    ))}
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td
                                    colSpan={columns.length}
                                    className="px-4 py-6 text-center text-sm text-gray-500"
                                >
                                    {emptyText}
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </InfiniteScroll>
        </div>
    )
}
