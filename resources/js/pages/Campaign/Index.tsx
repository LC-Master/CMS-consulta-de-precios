import { InfiniteScroll } from '@inertiajs/react'

export default function CampaignsIndex({ campaigns }) {
    console.log(campaigns)
    return (
        <div className="overflow-x-auto">
            <InfiniteScroll data="campaigns">
                <table className="min-w-full bg-white divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">ID</th>
                            <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Título</th>
                            <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Estado</th>
                            <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Creada</th>
                            <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-100">
                        {Array.isArray(campaigns?.data) && campaigns.data.length > 0 ? (
                            campaigns.data.map((campaign) => (
                                <tr key={campaign.id} className="hover:bg-gray-50">
                                    <td className="px-4 py-3 text-sm text-gray-900">{campaign.id}</td>
                                    <td className="px-4 py-3 text-sm text-gray-900">{campaign.title}</td>
                                    <td className="px-4 py-3 text-sm text-gray-700">{campaign.status.status}</td>
                                    <td className="px-4 py-3 text-sm text-gray-700">
                                        {campaign.created_at ? new Date(campaign.created_at).toLocaleString() : '-'}
                                    </td>
                                    <td className="px-4 py-3 text-sm">
                                        <a
                                            href={`/campaigns/${campaign.id}`}
                                            className="text-blue-600 hover:underline"
                                        >
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            ))
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
    )
}