import AppLayout from '@/layouts/app-layout'
import { useForm, Link, Head } from '@inertiajs/react'
import Select from 'react-select'
import { Center, Department, Option, MediaItem, } from '@/types/campaign/index.types'
import { Input } from '@/components/ui/input'
import React from 'react'
import { Button } from '@/components/ui/button'
import useModal from '@/hooks/use-modal'
import { CampaignCreateProps } from '@/types/campaign/page.type'
import UploadMediaModal from '@/components/modals/UploadMediaModal'
import useToast from '@/hooks/use-toast'
import { useMediaSync } from '@/hooks/use-mediasync'
import { index, store } from '@/routes/campaign'
import MediaColumn from '@/components/campaign/MediaColumn'
import MediaList from '@/components/campaign/MediaList'
import { breadcrumbs } from '@/helpers/breadcrumbs'
import useSearch from '@/hooks/use-search'
import { useMediaActions } from '@/hooks/use-media-actions'
import { Agreement } from '@/types/agreement/index.types'
import { CircleAlert, PlusCircle, Save, SquarePlay } from 'lucide-react'
import { Spinner } from '@/components/ui/spinner'
import { Label } from '@/components/ui/label'

export default function CampaignCreate({ centers, departments, agreements, media, flash }: CampaignCreateProps) {
    const { isOpen, openModal, closeModal } = useModal(false)
    const ToastComponent = useToast(flash)
    const { mediaList, setMediaList, pm, setPm, am, setAm } = useMediaSync(media);
    const { handlerSearch, search, filteredItems } = useSearch(mediaList);
    const { moveUp, moveDown, transfer } = useMediaActions<MediaItem>();
    const { data, setData, processing, errors, post, transform, cancel } = useForm({
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
    const filteredCenters = (val: Option[] | null): void => {
        const selected = val ?? []
        const values = selected.map(v => v.value)
        const todoValue = optionsCenter.find(o => /\bTodo\b/i.test(o.label))?.value

        if (todoValue && values.includes(todoValue)) {
            setData('centers', [todoValue])
        } else {
            setData('centers', todoValue ? values.filter(v => v !== todoValue) : values)
        }
    }
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault()
        transform(data => ({
            ...data,
            am_media: am.map(item => item.id),
            pm_media: pm.map(item => item.id),
        }))
        post(store().url, { preserveScroll: true, forceFormData: true })
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs('Crear Campaña', index().url)}>
            {ToastComponent.ToastContainer()}
            <Head title="Crear Campaña" />
            <div className="p-6 space-y-6">
                <div className='ml-2'>
                    <h1 className="text-3xl font-bold">Crea una nueva campaña</h1>
                    <p className='text-gray-600 '>Complete el formulario a continuación para crear una nueva campaña.</p>
                </div>
                <div className="shadow-[0_0_20px_rgba(0,0,0,0.08)] rounded-lg bg-white">
                    <form id="form" method="post" onSubmit={handleSubmit} className="space-y-6" action={store().url} noValidate>
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
                                <Label htmlFor="department_id" className="block text-sm font-bold mb-4 ml-1 text-gray-700">Departamento. *</Label>
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

                        <div className="grid grid-cols-1 md:grid-cols-2 pl-6 pr-6 gap-4">
                            <div>
                                <Label htmlFor="start_at" className="block text-sm font-bold mb-4 ml-1 text-gray-700">Fecha y hora (inicio). *</Label>
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
                                <Label htmlFor="end_at" className="block text-sm font-bold mb-4 ml-1 text-gray-700">Fecha y hora (fin). *</Label>
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
                        <div className="grid grid-cols-1 md:grid-cols-2 pl-6 pr-6 gap-4">
                            <div>
                                <Label htmlFor="centers" className="block text-sm font-bold mb-4 ml-1 text-gray-700">Centros. *</Label>
                                <Select<Option, true>
                                    options={optionsCenter}
                                    inputId="centers"
                                    value={optionsCenter.filter(o => data.centers.includes(o.value))}
                                    name="centers"
                                    required={false}
                                    isMulti
                                    className="mt-1 rounded-md"
                                    classNamePrefix="react-select"
                                    onChange={val => filteredCenters(val as Option[] | null)}
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
                                <Label htmlFor="agreement_id" className="block text-sm font-bold mb-4 ml-1 text-gray-700">Acuerdo</Label>
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
                                <div className="flex w-full pl-6 pr-6 gap-4 mt-8 mb-4">
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