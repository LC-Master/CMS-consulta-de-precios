import AppLayout from '@/layouts/app-layout'
import { index } from '@/routes/campaign'
import { BreadcrumbItem } from '@/types'
import { useForm, router } from '@inertiajs/react'
import Select from 'react-select'
import { Center, Department, Option, Agreement, MediaItem, } from '@/types/campaign/index.types'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import React, { useState } from 'react'
import { Button } from '@/components/ui/button'
import useModal from '@/hooks/use-modal'
import { CampaignCreateProps } from '@/types/campaign/page.type'
import UploadMediaModal from '@/components/modals/UploadMediaModal'

export default function CampaignCreate({ centers, departments, agreements, media }: CampaignCreateProps) {
    const { isOpen, openModal, closeModal } = useModal(false)
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
    console.log(media)
    const [pm, setPm] = useState<MediaItem[]>([])
    const [am, setAm] = useState<MediaItem[]>([])
    const [mediaList, setMediaList] = useState<MediaItem[]>(() => (media ? [...media] : []))

    const { data, setData, processing, errors, post } = useForm({
        title: '',
        start_at: '',
        end_at: '',
        centers: [] as string[],
        department_id: '',
        agreement_id: '',
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
        post('/campaign')
    }
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="p-6 space-y-6">
                <form id="form" method="post" onSubmit={handleSubmit} className="space-y-4" action="/campaigns" noValidate>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label htmlFor="title" className="block text-sm font-medium text-gray-700">Título</label>
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
                            <label htmlFor="department_id" className="block text-sm font-medium text-gray-700">Departamento</label>
                            <Select<Option, false>
                                options={optionsDepartment}
                                inputId="department_id"
                                value={optionsDepartment.find(o => o.value === data.department_id) || null}
                                name="department_id"
                                className="mt-1 rounded-md"
                                classNamePrefix="react-select"
                                onChange={(val) => setData('department_id', (val as Option | null)?.value ?? '')}
                                placeholder="Selecciona un departamento"
                                isClearable
                                aria-required={false}
                                aria-invalid={!!errors.department_id}
                                aria-describedby={errors.department_id ? 'department_id-error' : undefined}
                            />
                            {errors.department_id && <p id="department_id-error" role="alert" className="text-red-500 text-sm mt-1">{errors.department_id}</p>}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label htmlFor="start_at" className="block text-sm font-medium text-gray-700">Fecha y hora (inicio)</label>
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
                            <label htmlFor="end_at" className="block text-sm font-medium text-gray-700">Fecha y hora (fin)</label>
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
                        <label htmlFor="centers" className="block text-sm font-medium text-gray-700">Centros</label>
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
                            className="mt-1 rounded-md"
                            classNamePrefix="react-select"
                            onChange={(val) => setData('agreement_id', (val as Option | null)?.value ?? '')}
                            placeholder="Selecciona un acuerdo"
                            isClearable
                            aria-required={false}
                            aria-invalid={!!errors.agreement_id}
                            aria-describedby={errors.agreement_id ? 'agreement_id-error' : undefined}
                        />
                        {errors.agreement_id && <p id="agreement_id-error" role="alert" className="text-red-500 text-sm mt-1">{errors.agreement_id}</p>}
                    </div>

                    <div className='flex flex-row justify-between items-center'>
                        <label className="block text-sm font-medium text-gray-700 mb-2">Multimedia</label>
                        <Button type='button' onClick={openModal}>Agregar Multimedia</Button>
                        {isOpen && <UploadMediaModal closeModal={closeModal} success={handleSuccess} />}

                    </div>
                    <div>
                        <div className="flex w-full gap-4 mb-4">
                            <div className="w-1/2">
                                <Label htmlFor='am'>AM</Label>
                                <div className="h-60 max-h-60 overflow-y-auto border border-gray-300 rounded p-2">
                                    {am.map((item: MediaItem) => {
                                        return (
                                            <div key={item.id} className="p-2 border flex flex-row gap-2 justify-between rounded mb-2">
                                                <div>
                                                    <p><strong>Nombre:</strong> {item.name}</p>
                                                    <p><strong>Tipo:</strong> {item.mime_type}</p>
                                                </div>
                                                <div className="mt-2 flex items-center gap-2">
                                                    <Button type="button" onClick={(e: React.MouseEvent<HTMLButtonElement>) => {
                                                        e.preventDefault()
                                                        setPm([...pm, item])
                                                        setAm(am.filter(m => m.id !== item.id))
                                                    }}>
                                                        PM
                                                    </Button>
                                                    <Button type="button" onClick={(e: React.MouseEvent<HTMLButtonElement>) => {
                                                        e.preventDefault()
                                                        const index = am.findIndex(m => m.id === item.id)
                                                        if (index > 0) {
                                                            const newAm = [...am]
                                                            const temp = newAm[index - 1]
                                                            newAm[index - 1] = newAm[index]
                                                            newAm[index] = temp
                                                            setAm(newAm)
                                                        }
                                                    }}>
                                                        ↑
                                                    </Button>
                                                    <Button type="button" onClick={(e: React.MouseEvent<HTMLButtonElement>) => {
                                                        e.preventDefault()
                                                        const index = am.findIndex(m => m.id === item.id)
                                                        if (index < am.length - 1) {
                                                            const newAm = [...am]
                                                            const temp = newAm[index + 1]
                                                            newAm[index + 1] = newAm[index]
                                                            newAm[index] = temp
                                                            setAm(newAm)
                                                        }
                                                    }}>
                                                        ↓
                                                    </Button>
                                                </div>
                                            </div>
                                        )
                                    })}
                                </div>
                            </div>

                            <div className="w-1/2">
                                <Label htmlFor='pm'>PM</Label>
                                <div className="h-60 max-h-60 overflow-y-auto border border-gray-300 rounded p-2">
                                    {pm.map((item: MediaItem) => {
                                        return (
                                            <div key={item.id} className="p-2 border flex flex-row gap-2 justify-between rounded mb-2">
                                                <div>
                                                    <p><strong>Nombre:</strong> {item.name}</p>
                                                    <p><strong>Tipo:</strong> {item.mime_type}</p>
                                                </div>
                                                <div className="mt-2 flex items-center gap-2">
                                                    <Button type='button' onClick={(e: React.MouseEvent<HTMLButtonElement, MouseEvent>) => {
                                                        e.preventDefault()
                                                        setAm([...am, item])
                                                        setPm(pm.filter(m => m.id !== item.id))
                                                    }}>
                                                        AM
                                                    </Button>
                                                    <Button type="button" onClick={(e: React.MouseEvent<HTMLButtonElement>) => {
                                                        e.preventDefault()
                                                        const index = pm.findIndex(m => m.id === item.id)
                                                        if (index > 0) {
                                                            const newPm = [...pm]
                                                            const temp = newPm[index - 1]
                                                            newPm[index - 1] = newPm[index]
                                                            newPm[index] = temp
                                                            setPm(newPm)
                                                        }
                                                    }}>
                                                        ↑
                                                    </Button>
                                                    <Button type="button" onClick={(e: React.MouseEvent<HTMLButtonElement>) => {
                                                        e.preventDefault()
                                                        const index = pm.findIndex(m => m.id === item.id)
                                                        if (index < pm.length - 1) {
                                                            const newPm = [...pm]
                                                            const temp = newPm[index + 1]
                                                            newPm[index + 1] = newPm[index]
                                                            newPm[index] = temp
                                                            setPm(newPm)
                                                        }
                                                    }}>
                                                        ↓
                                                    </Button>
                                                </div>
                                            </div>
                                        )
                                    })}
                                </div>
                            </div>
                        </div>
                        <div>
                            <Label htmlFor='media'>Media content</Label>
                            <div id='media' className="max-h-60 overflow-y-auto border border-gray-300 rounded p-2">
                                {mediaList.map((item: MediaItem) => {
                                    return (
                                        <div key={item.id} className="p-2 border flex flex-row gap-2 justify-between  rounded mb-2">
                                            <div>
                                                <p><strong>Nombre:</strong> {item.name}</p>
                                                <p><strong>Tipo:</strong> {item.mime_type}</p>
                                            </div>
                                            <div className="mt-2 flex gap-2">
                                                <Button type='button' onClick={(e: React.MouseEvent<HTMLButtonElement, MouseEvent>) => {
                                                    e.preventDefault()
                                                    setAm([...am, item])
                                                    setMediaList(mediaList.filter(m => m.id !== item.id))
                                                }}>
                                                    AM
                                                </Button>
                                                <Button type='button' onClick={(e: React.MouseEvent<HTMLButtonElement, MouseEvent>) => {
                                                    e.preventDefault()
                                                    setPm([...pm, item])
                                                    setMediaList(mediaList.filter(m => m.id !== item.id))
                                                }}>
                                                    PM
                                                </Button>
                                            </div>
                                        </div>
                                    )
                                })}
                            </div>
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