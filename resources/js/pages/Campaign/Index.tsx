import { useState } from 'react'
import { InfiniteScroll, router } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout';
import { useUpdateEffect } from '@/hooks/useUpdateEffect';
import { Props } from '@/types/campaign/index.types';
import { Filter } from '@/components/Filter';



export default function CampaignsIndex({ campaigns, filters = {}, statuses = [] }: Props) {
    const [search, setSearch] = useState(filters.search || '')
    const [status, setStatus] = useState(filters.status || '')
    useUpdateEffect(() => {
        router.get(
            window.location.pathname,
            { search, status },
            { preserveState: true, replace: true, preserveScroll: true }
        )
    }, [search, status])
    return (
        <AppLayout>
            <div className="space-y-4 px-4 pb-4">
                <Filter
                    filters={[
                        {
                            type: 'search',
                            key: 'search',
                            value: search,
                            placeholder: 'Buscar por título...',
                            onChange: setSearch,
                        },
                        {
                            type: 'select',
                            key: 'status',
                            value: status,
                            placeholder: 'Filtrar por estado',
                            options: [
                                { value: '', label: 'Todos' },
                                ...statuses.map(s => ({
                                    value: s.id,
                                    label: s.status,
                                })),
                            ],
                            onChange: setStatus,
                        },
                        
                    ]}
                />
                <div className="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                    <InfiniteScroll data="campaigns">
                        <table className="min-w-full bg-white divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Título</th>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Estado</th>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Creada</th>
                                    <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-100">
                                {Array.isArray(campaigns?.data) && campaigns.data.length > 0 ? (
                                    campaigns.data.map((campaign) => {
                                        return (
                                            <tr key={campaign.id} className="hover:bg-gray-50">
                                                <td className="px-4 py-3 text-sm text-gray-900">{campaign.title}</td>
                                                <td className="px-4 py-3 text-sm text-gray-700">{campaign.status?.status}</td>
                                                <td className="px-4 py-3 text-sm text-gray-700">
                                                    {campaign.created_at ? new Date(campaign.created_at).toLocaleString() : '-'}
                                                </td>
                                                <td className="px-4 py-3 text-sm">
                                                    <a
                                                        href={`/campaign/${campaign.id}`}
                                                        className="text-blue-600 hover:underline"
                                                    >
                                                        Ver
                                                    </a>
                                                </td>
                                            </tr>
                                        )
                                    })
                                ) : (
                                    <tr>
                                        <td colSpan={5} className="px-4 py-6 text-center text-sm text-gray-500">
                                            No hay campañas
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </InfiniteScroll>
                </div>
            </div>
        </AppLayout>
    )
}