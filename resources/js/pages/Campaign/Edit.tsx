import AppLayout from '@/layouts/app-layout'
import { useForm, Link, Head } from '@inertiajs/react'
import Select from 'react-select'
import { Option } from '@/types/campaign/index.types'
import { Input } from '@/components/ui/input'
import useSearch from '@/hooks/use-search'
import { Button } from '@/components/ui/button'
import useModal from '@/hooks/use-modal'
import { CampaignEditProps } from '@/types/campaign/page.type'
import UploadMediaModal from '@/components/modals/UploadMediaModal'
import useToast from '@/hooks/use-toast'
import { useMediaSync } from '@/hooks/use-mediasync'
import { index, update } from '@/routes/campaign'
import MediaColumn from '@/components/campaign/MediaColumn'
import MediaList from '@/components/campaign/MediaList'
import { breadcrumbs } from '@/helpers/breadcrumbs'
import { useMediaActions } from '@/hooks/use-media-actions'
import InputError from '@/components/input-error'
import useLoadOptions from '@/hooks/use-load-options'
import useLoadEdit from '@/hooks/use-load-edit'
import { useEffect } from 'react'
import { CircleAlert, PlusCircle, Save, SquarePlay } from 'lucide-react'
import { Spinner } from '@/components/ui/spinner'
import { Label } from '@/components/ui/label'
import { MediaItem } from '@/types/media/index.type'
import GroupedSelect from '@/components/ui/GroupedSelect'

export default function CampaignEdit({ departments, stores, agreements, media, flash, campaign }: CampaignEditProps) {
    const { mediaList, setMediaList, pm, setPm, am, setAm } = useMediaSync(media);
    const { isOpen, openModal, closeModal } = useModal(false)
    useLoadEdit(campaign, setAm, setPm)
    const { optionsDepartment, optionsAgreement } = useLoadOptions(departments, agreements)
    const ToastComponent = useToast(flash)
    const { moveDown, moveUp, transfer, removeItem } = useMediaActions<MediaItem>()
    const { handlerSearch, search, filteredItems } = useSearch<MediaItem>(mediaList)
    
    const { data, setData, processing, errors, put, transform, cancel } = useForm({
        title: campaign.title || '',
        start_at: campaign.start_at ? new Date(campaign.start_at).toISOString().slice(0, 16) : '',
        end_at: campaign.end_at ? new Date(campaign.end_at).toISOString().slice(0, 16) : '',
        stores: campaign.stores ? campaign.stores.map(store => store.id) : [],
        department_id: String(campaign.department_id || ''),
        agreements: campaign.agreements ? campaign.agreements.map(agreement => agreement.id) : [],
        am_media: campaign.media.filter(item => item.slot === 'am').map(item => item.id) || [],
        pm_media: campaign.media.filter(item => item.slot === 'pm').map(item => item.id) || [],
    })
    const handleSelection = (ids: string[]) => {
        setData('stores', ids);
    };
    useEffect(() => {
        transform((data) => ({
            ...data,
            am_media: am.map(item => item.id),
            pm_media: pm.map(item => item.id),
        }))
    }, [am, pm, transform])

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault()
        put(update({ id: campaign.id }).url, {
            preserveScroll: true
        })
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs('Editar campaña', index().url)}>
            {ToastComponent.ToastContainer()}
            <Head title="Edición de campaña" />
            <div className="p-6 space-y-6">
                <div className='ml-2'>
                    <h1 className="text-3xl font-bold">Edición de campaña</h1>
                    <p className='text-gray-600 '>Complete el formulario a continuación para editar la campaña.</p>
                </div>
                <div className="shadow-[0_0_20px_rgba(0,0,0,0.08)] rounded-lg bg-white">
                    <form id="form" onSubmit={handleSubmit} className="space-y-6" noValidate>
                        <div className='flex items-center gap-2 pl-6 pr-6 mb-4 pt-6 pb-2'>
                            <CircleAlert />
                            <h2 className='text-bold font-bold text-lg text-gray-900'>
                                Información General
                            </h2>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-2 pl-6 pr-6 gap-4">
                            <div>
                                <Label htmlFor="title" className="block text-sm font-bold mb-4 ml-1 text-gray-700">Título. *</Label>
                                <Input
                                    type="text"
                                    id="title"
                                    value={data.title}
                                    required
                                    placeholder="Título de la campaña"
                                    autoComplete="off"
                                    onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('title', e.target.value)}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                                />
                                <InputError message={errors.title} />
                            </div>

                            <div>
                                <Label htmlFor="department_id" className="block text-sm font-bold mb-4 ml-1 text-gray-700">Departamento / Categoría. *</Label>
                                <Select<Option, false>
                                    options={optionsDepartment}
                                    inputId="department_id"
                                    value={optionsDepartment.find(o => o.value === data.department_id) || null}
                                    onChange={(val) => setData('department_id', (val as Option | null)?.value ?? '')}
                                    placeholder="Selecciona un departamento"
                                    isClearable
                                />
                                <InputError message={errors.department_id} />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 pl-6 pr-6 gap-4">
                            <div>
                                <Label htmlFor="start_at" className="block text-sm font-bold mb-4 ml-1 text-gray-700">Fecha y hora (inicio). *</Label>
                                <Input
                                    type="datetime-local"
                                    id="start_at"
                                    value={data.start_at}
                                    required
                                    onChange={e => setData('start_at', e.target.value)}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio text-left [&::-webkit-calendar-picker-indicator]:ml-auto [&::-webkit-calendar-picker-indicator]:cursor-pointer"
                                />
                                <InputError message={errors.start_at} />
                            </div>

                            <div>
                                <Label htmlFor="end_at" className="block text-sm font-bold mb-4 ml-1 text-gray-700">Fecha y hora (fin). *</Label>
                                <Input
                                    type="datetime-local"
                                    id="end_at"
                                    value={data.end_at}
                                    required
                                    onChange={e => setData('end_at', e.target.value)}
                                    className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio text-left [&::-webkit-calendar-picker-indicator]:ml-auto [&::-webkit-calendar-picker-indicator]:cursor-pointer"
                                />
                                <InputError message={errors.end_at} />
                            </div>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-2 pl-6 pr-6 gap-4">
                            <div>
                                <Label htmlFor="centers" className="block text-sm font-bold mb-4 ml-1 text-gray-700">Centros. *</Label>
                                <GroupedSelect
                                    dataFromBackend={stores}
                                    onSelectionChange={handleSelection}
                                    initialSelectedIds={data.stores}
                                />
                                <InputError message={errors.stores} id="centers-error" />
                            </div>

                            <div>
                                <Label htmlFor="agreement_id" className="block text-sm font-bold mb-4 ml-1 text-gray-700">Acuerdo</Label>
                                <Select<Option, true>
                                    options={optionsAgreement}
                                    inputId="agreement_id"
                                    value={optionsAgreement.filter(o => data.agreements.includes(o.value))}
                                    onChange={(val) => setData('agreements', (val as Option[]).map(v => v.value))}
                                    placeholder="Selecciona un acuerdo"
                                    isClearable
                                    isMulti
                                />
                                <InputError message={errors.agreements} />
                            </div>
                        </div>
                        <div className='pt-12 border-t-2 border-gray-100 w-full'>
                            <div className='flex flex-row pl-6 pr-6 justify-between items-center'>
                                <div className='flex items-center gap-2 mb-2'>
                                    <SquarePlay />
                                    <Label className="block text-xl font-bold ">Programación Multimedia</Label>
                                </div>
                                <Button type='button' className='bg-locatel-medio hover:bg-locatel-oscuro' onClick={openModal}>
                                    <PlusCircle />
                                    Agregar Multimedia
                                </Button>
                                {isOpen && <UploadMediaModal closeModal={closeModal} />}
                            </div>

                            <div>
                                <div className="flex w-full pl-6 pr-6 gap-4 mt-8 mb-4 flex-wrap md:flex-nowrap">
                                    <MediaColumn
                                        title="AM"
                                        items={am}
                                        onMoveToOther={(item) => transfer(item, setAm, setPm)}
                                        onMoveUp={(id) => moveUp(id, setAm)}
                                        onMoveDown={(id) => moveDown(id, setAm)}
                                        onRemove={(item) => removeItem(item, setAm)}
                                        errors={errors.am_media}
                                    />
                                    <MediaColumn
                                        title="PM"
                                        items={pm}
                                        onMoveToOther={(item) => transfer(item, setPm, setAm)}
                                        onMoveUp={(id) => moveUp(id, setPm)}
                                        onMoveDown={(id) => moveDown(id, setPm)}
                                        onRemove={(item) => removeItem(item, setPm)}
                                        errors={errors.pm_media}
                                    />
                                </div>

                                <div className="pl-6 pr-6 mb-6">
                                    <MediaList
                                        value={search}
                                        onSearch={handlerSearch}
                                        mediaList={filteredItems}
                                        onMoveToAm={item => transfer(item, setMediaList, setAm, true)}
                                        onMoveToPm={item => transfer(item, setMediaList, setPm, true)}
                                    />
                                </div>
                            </div>
                        </div>
                    </form>

                    <div className="flex flex-wrap w-full p-6 border-t shadow-t-lg border-gray-200 bg-[#fcfcfc] justify-center gap-3">
                        <Button
                            type="submit"
                            form="form"
                            className="bg-locatel-medio hover:bg-locatel-oscuro active:bg-locatel-oscuro flex flex-row items-center h-12 text-white rounded-md px-6 py-3 shadow hover:brightness-95 disabled:opacity-50"
                            disabled={processing}
                        >
                            {processing ? (<><Spinner /> Guardando....</>) : <><Save /> Guardar</>}
                        </Button>

                        <Link
                            viewTransition
                            href={index().url}
                            onClick={() => { if (processing) cancel() }}
                            className="bg-red-500 text-white rounded-md px-6 py-3 shadow hover:brightness-95"
                        >
                            Cancelar
                        </Link>
                    </div>
                </div>
            </div>
        </AppLayout >
    )
}