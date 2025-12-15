import { useState, useMemo } from 'react'
import { router } from '@inertiajs/react'
import { Eye, Trash, Search, ChevronDown, ChevronUp } from 'lucide-react'
import AppLayout from '@/layouts/app-layout';
import { useUpdateEffect } from '@/hooks/useUpdateEffect';
import { Column, DataTable } from '@/components/DataTable';
import { Media as BaseMedia, Props as BaseProps } from '@/types/media/index.types';
import AnchorIcon from '@/components/ui/AnchorIcon';
import DeleteIcon from '@/components/ui/DeleteIcon';
import Select from 'react-select';

// --- Tipos ---
interface Campaign {
    id: string;
    title: string;
}

interface Media extends BaseMedia {
    campaigns?: Campaign[];
}

interface Props extends BaseProps {
    medias: { data: Media[] };
    filters: { search?: string; type?: string };
    mimeTypes: string[];
}

// --- Función Helper para formatear Bytes ---
const formatBytes = (bytes: number | string, decimals = 2) => {
    const value = Number(bytes);
    if (!value) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(value) / Math.log(k));
    return `${parseFloat((value / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
}

// --- Sub-componente para manejar la expansión de campañas ---
const ExpandableCampaignList = ({ campaigns }: { campaigns?: Campaign[] }) => {
    const [isExpanded, setIsExpanded] = useState(false);
    const maxVisible = 2;

    if (!campaigns || campaigns.length === 0) {
        return <span className="text-gray-400 italic text-xs">Sin asignar</span>;
    }

    const visibleCampaigns = isExpanded ? campaigns : campaigns.slice(0, maxVisible);
    const remainingCount = campaigns.length - maxVisible;

    return (
        <div className="flex flex-col items-start gap-1 min-w-[150px]">
            <div className="flex flex-wrap items-center gap-1">
                {visibleCampaigns.map((camp) => (
                    <a 
                        key={camp.id} 
                        href={`/campaign/${camp.id}`}
                        // CORRECCIÓN AQUÍ: Se eliminó 'truncate' y 'max-w-[120px]'
                        // para que el nombre se vea completo.
                        className="underline inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100"
                        title={camp.title}
                    >
                        {camp.title}
                    </a>
                ))}

                {/* Botón para expandir (+N) */}
                {!isExpanded && remainingCount > 0 && (
                    <button 
                        onClick={() => setIsExpanded(true)}
                        className="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-gray-300"
                        title="Clic para ver todas"
                    >
                        +{remainingCount}
                        <ChevronDown className="w-3 h-3" />
                    </button>
                )}
            </div>

            {/* Botón para contraer (Ver menos) */}
            {isExpanded && remainingCount > 0 && (
                <button 
                    onClick={() => setIsExpanded(false)}
                    className="text-xs text-gray-500 hover:text-gray-700 underline inline-flex items-center gap-0.5 mt-0.5 focus:outline-none"
                >
                   Ver menos <ChevronUp className="w-3 h-3" />
                </button>
            )}
        </div>
    );
};


export default function MediaIndex({ medias, filters = {}, mimeTypes = [] }: Props) {
    const [search, setSearch] = useState(filters.search || '')
    const [type, setType] = useState(filters.type || '')

    const typeOptions = useMemo(() => {
        return [
            { value: '', label: 'Todos los tipos' },
            ...mimeTypes.map(mime => ({ value: mime, label: mime }))
        ];
    }, [mimeTypes]);

    const columns: Column<Media>[] = [
        {
            key: 'name',
            header: 'Nombre',
            render: (a) => (
                <div className="flex flex-col max-w-[200px]">
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
            render: (a) => a.duration_seconds ? `${a.duration_seconds}s` : '-',
        },
        {
            key: 'campaigns',
            header: 'Campaña',
            render: (media) => <ExpandableCampaignList campaigns={media.campaigns} />,
        },
        {
            key: 'actions',
            header: 'Acciones',
            render: (a) => (
                <div className="flex gap-2">
                    <AnchorIcon url={`/media/${a.id}`} icon={Eye} />
                    <DeleteIcon url={`/media/${a.id}`} icon={Trash} />
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
        <AppLayout>
            <div className="space-y-4 px-4 pb-4">
                
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
                    data={medias.data}
                    columns={columns}
                    rowKey={(a) => a.id}
                    infiniteData="medias"
                />

            </div>
        </AppLayout>
    )
}