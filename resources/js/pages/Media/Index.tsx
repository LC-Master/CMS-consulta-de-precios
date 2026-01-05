import { useState, useMemo } from 'react'
import { router } from '@inertiajs/react'
import { Eye, Trash, Search } from 'lucide-react'
import AppLayout from '@/layouts/app-layout';
import { useUpdateEffect } from '@/hooks/useUpdateEffect';
import { Column, DataTable } from '@/components/DataTable';
import AnchorIcon from '@/components/ui/AnchorIcon';
import Select from 'react-select';
import { MediaItem, Props } from '@/types/campaign/index.types';
import { Button } from '@/components/ui/button';
import useToast from '@/hooks/use-toast';
import { breadcrumbs } from '@/helpers/breadcrumbs';
import { index } from '@/routes/media';
import { formatBytes } from '@/helpers/mediaTools';
import { show as showMedia } from '@/routes/media';
import useModal from '@/hooks/use-modal';
import ExpandableCampaignList from '@/components/ui/ExpandableCampaignList';
import DeleteMedia from '@/components/modals/DeleteMedia';

export default function MediaIndex({ medias, filters = {}, mimeTypes = [], flash }: Props) {
    const { isOpen, closeModal, openModal } = useModal(false)
    const [mediaId, setMediaId] = useState<string>('');
    const [search, setSearch] = useState(filters.search || '')
    const [type, setType] = useState(filters.type || '')
    const typeOptions = useMemo(() => {
        return [
            { value: '', label: 'Todos los tipos' },
            ...mimeTypes.map(mime => ({ value: mime, label: mime }))
        ];
    }, [mimeTypes]);
    const { ToastContainer } = useToast(flash);
    const columns: Column<MediaItem>[] = [
        {
            key: 'name',
            header: 'Nombre',
            render: (a) => (
                <div className="flex flex-col max-w-50">
                    <span className="font-medium text-gray-900 truncate" title={a.name}>
                        {a.name}
                    </span>
                </div>
            ),
        },
        {
            key: 'mime_type',
            header: 'Tipo',
            render: (a) => (
                <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                    {a.mime_type}
                </span>
            ),
        },
        {
            key: 'size',
            header: 'Tamaño',
            render: (a) => <span className="text-sm text-gray-600 whitespace-nowrap">{formatBytes(a.size)}</span>,
        },
        {
            key: 'duration_seconds',
            header: 'Duración',
            render: (a) => a.duration_seconds ? `${Math.round(a.duration_seconds)}s` : '-',
        },
        {
            key: 'campaigns',
            header: 'Campaña',
            render: (media) => {
                return <ExpandableCampaignList campaigns={media.campaigns} />
            },
        },
        {
            key: 'actions',
            header: 'Acciones',
            render: (a) => (
                <div className="flex gap-2">
                    <AnchorIcon href={showMedia({ id: a.id }).url} icon={Eye} />
                    <Button variant="destructive" className='h-8' title="Eliminar media" onClick={() => {
                        openModal()
                        setMediaId(a.id);
                    }}>
                        <Trash className='w-4 h-4' />
                    </Button>
                </div>
            ),
        },
    ]

    useUpdateEffect(() => {
        router.get(
            window.location.pathname,
            { search, type },
            { preserveState: true, replace: true, preserveScroll: true }
        )
    }, [search, type])

    return (
        <AppLayout breadcrumbs={breadcrumbs('Lista de multimedia', index().url)}>
            {isOpen && (<DeleteMedia closeModal={closeModal} mediaId={mediaId} setMediaId={setMediaId} />)}
            <div className="space-y-4 px-4 pb-4">
                {ToastContainer()}
                <div className="flex flex-col sm:flex-row gap-4 mt-4 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <div className="relative flex-1">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4 h-4" />
                        <input
                            type="text"
                            placeholder="Buscar video o campaña..."
                            className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                        />
                    </div>

                    <div className="w-full sm:w-64">
                        <Select
                            options={typeOptions}
                            value={typeOptions.find(o => o.value === type) || typeOptions[0]}
                            onChange={(val) => setType(val ? val.value : '')}
                            placeholder="Tipo de archivo"
                            isClearable={false}
                            classNamePrefix="select"
                            className="text-sm"
                        />
                    </div>
                </div>

                <DataTable
                    data={medias?.data ?? []}
                    columns={columns}
                    rowKey={(a) => a.id}
                    infiniteData="medias"
                />

            </div>
        </AppLayout>
    )
}