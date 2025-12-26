import AppLayout from '@/layouts/app-layout'
import { useForm, router } from '@inertiajs/react'
import Select from 'react-select'
import { Option, MediaItem, } from '@/types/campaign/index.types'
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
import { breadcrumbs } from '@/tools/breadcrumbs'
import { useMediaActions } from '@/hooks/use-media-actions'
import InputError from '@/components/input-error'
import useLoadOptions from '@/hooks/use-load-options'
import useLoadEdit from '@/hooks/use-load-edit'


export default function CampaignEdit({ centers, departments, agreements, media, flash, campaign }: CampaignEditProps) {
    const { mediaList, setMediaList, pm, setPm, am, setAm } = useMediaSync(media);
    const { isOpen, openModal, closeModal } = useModal(false)
    useLoadEdit(campaign, setAm, setPm)
    const { optionsCenter, optionsDepartment, optionsAgreement } = useLoadOptions(centers, departments, agreements)
    const ToastComponent = useToast(flash)
    const { moveDown, moveUp, transfer } = useMediaActions<MediaItem>()
    const { handlerSearch, search } = useSearch<MediaItem>(media, setMediaList)
    const handleSuccess = () => {
        router.reload(
            {
                only: ['media'],
            }
        )
    }
    const { data, setData, processing, errors, put, transform } = useForm({
        title: campaign.title || '',
        start_at: campaign.start_at ? new Date(campaign.start_at).toISOString().slice(0, 16) : '',
        end_at: campaign.end_at ? new Date(campaign.end_at).toISOString().slice(0, 16) : '',
        centers: campaign.centers ? campaign.centers.map(center => String(center.id)) : [],
        department_id: String(campaign.department_id || ''),
        agreement_id: String(campaign.agreement_id || ''),
        am_media: campaign.media.filter(item => item.slot === 'am').map(item => item.id) || [] as string[],
        pm_media: campaign.media.filter(item => item.slot === 'pm').map(item => item.id) || [] as string[],
    })
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault()
        transform(data => ({
            ...data,
            am_media: am.map(item => item.id),
            pm_media: pm.map(item => item.id),
        }))
        put(update({ id: campaign.id }).url, { preserveScroll: true, })
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs('Editar campaña', index().url)} >
            {ToastComponent.ToastContainer()}
            <div className="p-6 space-y-6">
                <form id="form" onSubmit={handleSubmit} method={update({ id: campaign.id }).method} action={update({ id: campaign.id }).url} className="space-y-4">
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
                            <InputError message={errors.title} id="title-error" />
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
                            <InputError message={errors.department_id} id="department_id-error" />
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
                            <InputError message={errors.start_at} id="start_at-error" />
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
                            <InputError message={errors.end_at} id="end_at-error" />
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
                        <InputError message={errors.centers} id="centers-error" />
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
                        <InputError message={errors.agreement_id} id="agreement_id-error" />
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
                                onMoveToOther={(item) => transfer(item, setAm, setPm)}
                                onMoveUp={(id) => moveUp(id, setAm)}
                                onMoveDown={(id) => moveDown(id, setAm)}
                                onRemove={(item) => transfer(item, setAm, setMediaList)}
                                errors={errors.am_media}
                            />
                            <MediaColumn
                                title="PM"
                                items={pm}
                                onMoveToOther={(item) => transfer(item, setPm, setAm)}
                                onMoveUp={(id) => moveUp(id, setPm)}
                                onMoveDown={(id) => moveDown(id, setPm)}
                                onRemove={(item) => transfer(item, setPm, setMediaList)}
                                errors={errors.pm_media}
                            />
                        </div>

                        <div>
                            <MediaList
                                value={search}
                                onSearch={handlerSearch}
                                mediaList={mediaList}
                                onMoveToAm={item => transfer(item, setMediaList, setAm)}
                                onMoveToPm={item => transfer(item, setMediaList, setPm)}
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