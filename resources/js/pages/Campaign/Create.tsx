import AppLayout from '@/layouts/app-layout'
import { BreadcrumbItem } from '@/types'
import { useForm, router } from '@inertiajs/react'
import Select from 'react-select'
import { Center, Department, Option, Agreement, MediaItem, } from '@/types/campaign/index.types'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import React, { useState, ChangeEvent } from 'react'
import { Button } from '@/components/ui/button'
import useModal from '@/hooks/use-modal'
import { CampaignCreateProps } from '@/types/campaign/page.type'
import UploadMediaModal from '@/components/modals/UploadMediaModal'
import useToast from '@/hooks/use-toast'
import { useMediaSync } from '@/hooks/use-mediasync'
import { index } from '@/routes/campaign'

type MediaColumnProps = {
    title: string
    items: MediaItem[]
    onMoveToOther: (item: MediaItem) => void
    onMoveUp: (id: MediaItem['id']) => void
    onMoveDown: (id: MediaItem['id']) => void
    onRemove: (item: MediaItem) => void
    errors?: string
}

function MediaItemCard({ item, controls }: { item: MediaItem; controls: React.ReactNode }) {
    return (
        <div className="p-3 mb-2 border rounded-md flex items-center justify-between gap-4 bg-white shadow-sm">
            <div className="flex items-center gap-4 min-w-0">
                {item.mime_type.startsWith('image/') ? (
                    <img
                        src={`/media/cdn/${item.id}`}
                        alt={item.name}
                        className="w-24 h-14 object-cover rounded-md shrink-0" loading='lazy'
                    />
                ) : item.thumbnails ? (
                    <img
                        src={`/thumbnail/cdn/${item.thumbnails.id}`}
                        alt={`Thumbnail ${item.name}`}
                        className="w-24 h-14 object-cover rounded-md shrink-0" loading='lazy'
                    />
                ) : (
                    <div className="w-24 h-14 bg-gray-100 rounded-md flex items-center justify-center text-xs text-gray-500 shrink-0">
                        Sin vista previa
                    </div>
                )}

                <div className="min-w-0">
                    <p className="font-medium text-sm text-gray-800 truncate">{item.name}</p>
                    <p className="text-xs text-gray-500 mt-1">{item.mime_type}</p>
                </div>
            </div>

            <div className="flex items-center gap-2">
                {controls}
            </div>
        </div>
    )
}

function MediaColumn({ title, items, onMoveToOther, onMoveUp, onMoveDown, onRemove, errors }: MediaColumnProps) {
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


type MediaListProps = {
    mediaList: MediaItem[]
    onMoveToAm: (item: MediaItem) => void
    onMoveToPm: (item: MediaItem) => void
    value: string
    onSearch: (e: React.ChangeEvent<HTMLInputElement>) => void
}

function MediaList({ value, onSearch, mediaList, onMoveToAm, onMoveToPm }: MediaListProps) {
    return (
        <div>
            <Label htmlFor='media'>Contenido multimedia</Label>
            <Input
                id="media-search"
                name="media_search"
                type="search"
                value={value}
                placeholder="Buscar multimedia..."
                className="mb-2 mt-2"
                onChange={onSearch}
                aria-label="Buscar multimedia"
                aria-describedby="media-search-help"
                aria-controls="media"
                autoComplete="off"
            />
            <div id='media' className="min-h-40 max-h-60 overflow-y-auto border-2 border-gray-300 rounded-sm p-2">
                {mediaList.length !== 0 ? mediaList.map((item) => (
                    <MediaItemCard
                        key={item.id}
                        item={item}
                        controls={
                            <>
                                <Button
                                    type="button"
                                    className="px-3 py-1 text-sm"
                                    onClick={(e) => { e.preventDefault(); onMoveToAm(item) }}
                                    aria-label={`Mover ${item.name} a AM`}
                                >
                                    AM
                                </Button>

                                <Button
                                    type="button"
                                    className="px-3 py-1 text-sm"
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

export default function CampaignCreate({ centers, departments, agreements, media, flash }: CampaignCreateProps) {
    const { isOpen, openModal, closeModal } = useModal(false)
    const [search, setSearch] = useState<string>('')
    const handlerSearch = (e: ChangeEvent<HTMLInputElement>) => {
        setSearch(e.target.value)
        setMediaList(media?.filter(item => item.name.toLowerCase().includes(e.target.value.toLowerCase())) || [])
    }
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Crear campaña',
            href: index().url,
        },
    ];
    const handleSuccess = () => {
        router.reload(
            {
                only: ['media'],
            }
        )
    }
    const {
        mediaList, setMediaList,
        pm, setPm,
        am, setAm
    } = useMediaSync(media);
    const { data, setData, processing, errors, post, transform } = useForm({
        title: '',
        start_at: '',
        end_at: '',
        centers: [] as string[],
        department_id: '',
        agreement_id: '',
        am_media: [] as string[],
        pm_media: [] as string[],
    })
    const optionsCenter: Option[] = centers.map((center: Center) => {
        return { value: center.id, label: center.name + " - " + center.code }
    })
    const optionsDepartment: Option[] = departments.map((department: Department) => {
        return { value: department.id, label: department.name }
    })
    const optionsAgreement: Option[] = agreements.map((agreement: Agreement) => {
        return { value: agreement.id, label: agreement.name }
    })
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault()
        transform(data => ({
            ...data,
            am_media: am.map(item => item.id),
            pm_media: pm.map(item => item.id),
        }))
        post('/campaign', { preserveScroll: true, forceFormData: true })
    }
    const moveFromAmToPm = (item: MediaItem) => {
        setPm(prev => [...prev, item])
        setAm(prev => prev.filter(m => m.id !== item.id))
    }
    const amMoveUp = (id: MediaItem['id']) => {
        const index = am.findIndex(m => m.id === id)
        if (index > 0) {
            const newAm = [...am]
            const temp = newAm[index - 1]
            newAm[index - 1] = newAm[index]
            newAm[index] = temp
            setAm(newAm)
        }
    }
    const amMoveDown = (id: MediaItem['id']) => {
        const index = am.findIndex(m => m.id === id)
        if (index < am.length - 1 && index >= 0) {
            const newAm = [...am]
            const temp = newAm[index + 1]
            newAm[index + 1] = newAm[index]
            newAm[index] = temp
            setAm(newAm)
        }
    }
    const removeFromAmToMedia = (item: MediaItem) => {
        setMediaList(prev => [...prev, item])
        setAm(prev => prev.filter(m => m.id !== item.id))
    }
    const moveFromPmToAm = (item: MediaItem) => {
        setAm(prev => [...prev, item])
        setPm(prev => prev.filter(m => m.id !== item.id))
    }
    const pmMoveUp = (id: MediaItem['id']) => {
        const index = pm.findIndex(m => m.id === id)
        if (index > 0) {
            const newPm = [...pm]
            const temp = newPm[index - 1]
            newPm[index - 1] = newPm[index]
            newPm[index] = temp
            setPm(newPm)
        }
    }
    const pmMoveDown = (id: MediaItem['id']) => {
        const index = pm.findIndex(m => m.id === id)
        if (index < pm.length - 1 && index >= 0) {
            const newPm = [...pm]
            const temp = newPm[index + 1]
            newPm[index + 1] = newPm[index]
            newPm[index] = temp
            setPm(newPm)
        }
    }
    const removeFromPmToMedia = (item: MediaItem) => {
        setMediaList(prev => [...prev, item])
        setPm(prev => prev.filter(m => m.id !== item.id))
    }

    const moveMediaToAm = (item: MediaItem) => {
        setAm(prev => [...prev, item])
        setMediaList(prev => prev.filter(m => m.id !== item.id))
    }
    const moveMediaToPm = (item: MediaItem) => {
        setPm(prev => [...prev, item])
        setMediaList(prev => prev.filter(m => m.id !== item.id))
    }

    const ToastComponent = useToast(flash)
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            {ToastComponent.ToastContainer()}
            <div className="p-6 space-y-6">
                <form id="form" method="post" onSubmit={handleSubmit} className="space-y-4" action="/campaigns" noValidate>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label htmlFor="title" className="block text-sm font-medium text-gray-700">Título. *</label>
                            <Input
                                type="text"
                                id="title"
                                name="title"
                                value={data.title}
                                required
                                placeholder="Título de la campaña"
                                autoComplete="off"
                                aria-invalid={!!errors.title}
                                aria-describedby={errors.title ? 'title-error' : undefined}
                                onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('title', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.title && <p id="title-error" role="alert" className="text-red-500 text-sm mt-1">{errors.title}</p>}
                        </div>

                        <div>
                            <label htmlFor="department_id" className="block text-sm font-medium text-gray-700">Departamento. *</label>
                            <Select<Option, false>
                                options={optionsDepartment}
                                inputId="department_id"
                                value={optionsDepartment.find(o => o.value === data.department_id) || null}
                                name="department_id"
                                classNamePrefix="react-select"
                                onChange={(val) => setData('department_id', (val as Option | null)?.value ?? '')}
                                placeholder="Selecciona un departamento"
                                isClearable
                                aria-required={false}
                                aria-invalid={!!errors.department_id}
                                aria-describedby={errors.department_id ? 'department_id-error' : undefined}
                                styles={{
                                    control: (provided) => ({
                                        ...provided,
                                        borderColor: errors.department_id ? '#ef4444' : provided.borderColor,
                                        boxShadow: errors.department_id ? '0 0 0 1px rgba(239,68,68,0.25)' : provided.boxShadow,
                                        '&:hover': {
                                            borderColor: errors.department_id ? '#ef4444' : provided.borderColor,
                                        },
                                        borderRadius: '0.375rem',
                                    }),
                                }}
                            />
                            {errors.department_id && <p id="department_id-error" role="alert" className="text-red-500 text-sm mt-1">{errors.department_id}</p>}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label htmlFor="start_at" className="block text-sm font-medium text-gray-700">Fecha y hora (inicio). *</label>
                            <Input
                                type="datetime-local"
                                id="start_at"
                                name="start_at"
                                value={data.start_at}
                                required
                                onChange={e => setData('start_at', e.target.value)}
                                aria-invalid={!!errors.start_at}
                                aria-describedby={errors.start_at ? 'start_at-error' : undefined}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.start_at && <p id="start_at-error" role="alert" className="text-red-500 text-sm mt-1">{errors.start_at}</p>}
                        </div>

                        <div>
                            <label htmlFor="end_at" className="block text-sm font-medium text-gray-700">Fecha y hora (fin). *</label>
                            <Input
                                type="datetime-local"
                                id="end_at"
                                name="end_at"
                                value={data.end_at}
                                required
                                onChange={e => setData('end_at', e.target.value)}
                                aria-invalid={!!errors.end_at}
                                aria-describedby={errors.end_at ? 'end_at-error' : undefined}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.end_at && <p id="end_at-error" role="alert" className="text-red-500 text-sm mt-1">{errors.end_at}</p>}
                        </div>
                    </div>

                    <div>
                        <label htmlFor="centers" className="block text-sm font-medium text-gray-700">Centros. *</label>
                        <Select<Option, true>
                            options={optionsCenter}
                            inputId="centers"
                            value={optionsCenter.filter(o => data.centers.includes(o.value))}
                            name="centers"
                            required={false}
                            isMulti
                            className="mt-1 rounded-md"
                            classNamePrefix="react-select"
                            onChange={(val) => setData('centers', (val as Option[]).map(v => v.value))}
                            placeholder="Selecciona centros..."
                            aria-required={true}
                            aria-invalid={!!errors.centers}
                            aria-describedby={errors.centers ? 'centers-error' : undefined}
                            styles={{
                                control: (provided) => ({
                                    ...provided,
                                    borderColor: errors.centers ? '#ef4444' : provided.borderColor,
                                    boxShadow: errors.centers ? '0 0 0 1px rgba(239,68,68,0.25)' : provided.boxShadow,
                                    '&:hover': {
                                        borderColor: errors.centers ? '#ef4444' : provided.borderColor,
                                    },
                                    borderRadius: '0.375rem',
                                }),
                            }}
                        />
                        {errors.centers && <p id="centers-error" role="alert" className="text-red-500 text-sm mt-1">{errors.centers}</p>}
                    </div>

                    <div>
                        <label htmlFor="agreement_id" className="block text-sm font-medium text-gray-700">Acuerdo</label>
                        <Select<Option, false>
                            options={optionsAgreement}
                            inputId="agreement_id"
                            value={optionsAgreement.find(o => o.value === data.agreement_id) || null}
                            name="agreement_id"
                            classNamePrefix="react-select"
                            onChange={(val) => setData('agreement_id', (val as Option | null)?.value ?? '')}
                            placeholder="Selecciona un acuerdo"
                            isClearable
                            aria-required={false}
                            aria-invalid={!!errors.agreement_id}
                            aria-describedby={errors.agreement_id ? 'agreement_id-error' : undefined}
                            styles={{
                                control: (provided) => ({
                                    ...provided,
                                    borderColor: errors.agreement_id ? '#ef4444' : provided.borderColor,
                                    boxShadow: errors.agreement_id ? '0 0 0 1px rgba(239,68,68,0.25)' : provided.boxShadow,
                                    '&:hover': {
                                        borderColor: errors.agreement_id ? '#ef4444' : provided.borderColor,
                                    },
                                    borderRadius: '0.375rem',
                                }),
                            }}
                        />
                        {errors.agreement_id && <p id="agreement_id-error" role="alert" className="text-red-500 text-sm mt-1">{errors.agreement_id}</p>}
                    </div>

                    <div className='flex flex-row justify-between items-center'>
                        <label className="block text-sm font-medium text-gray-700 mb-2">Multimedia. *</label>
                        <Button type='button' onClick={openModal}>Agregar Multimedia</Button>
                        {isOpen && <UploadMediaModal closeModal={closeModal} success={handleSuccess} />}
                    </div>

                    <div>
                        <div className="flex w-full gap-4 mb-4">
                            <MediaColumn
                                title="AM"
                                items={am}
                                onMoveToOther={moveFromAmToPm}
                                onMoveUp={amMoveUp}
                                onMoveDown={amMoveDown}
                                onRemove={removeFromAmToMedia}
                                errors={errors.am_media}
                            />
                            <MediaColumn
                                title="PM"
                                items={pm}
                                onMoveToOther={moveFromPmToAm}
                                onMoveUp={pmMoveUp}
                                onMoveDown={pmMoveDown}
                                onRemove={removeFromPmToMedia}
                                errors={errors.pm_media}
                            />
                        </div>

                        <div>
                            <MediaList
                                value={search}
                                onSearch={handlerSearch}
                                mediaList={mediaList}
                                onMoveToAm={moveMediaToAm}
                                onMoveToPm={moveMediaToPm}
                            />
                        </div>
                    </div>
                </form>

                <div className="flex flex-wrap justify-center gap-3">
                    <button
                        type="submit"
                        form="form"
                        className="bg-locatel-medio text-white rounded-md px-6 py-3 shadow hover:brightness-95 disabled:opacity-50"
                        disabled={processing}
                    >
                        Guardar
                    </button>

                    <button
                        type="button"
                        onClick={() => (window.location.href = '/campaign')}
                        className="bg-red-500 text-white rounded-md px-6 py-3 shadow hover:brightness-95"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </AppLayout >
    )
}