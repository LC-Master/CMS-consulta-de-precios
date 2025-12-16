import AppLayout from '@/layouts/app-layout'
import { index } from '@/routes/campaign'
import { BreadcrumbItem } from '@/types'
import { usePage, useForm } from '@inertiajs/react'
import Select from 'react-select'
import { Center, Department, Option, Agreement, MediaItem, } from '@/types/campaign/index.types'
import { Input } from '@/components/ui/input'
import { useState } from 'react'
import {
    DragDropProvider,
} from "@dnd-kit/react";
import { move } from '@dnd-kit/helpers'

import DropZone from '@/components/dnd/DropZone'
import MediaItemElement from '@/components/dnd/MediaItemElement'
export default function CampaignCreate() {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Crear campaña',
            href: index().url,
        },
    ];

    const { centers, departments, agreements, media } = usePage<{ centers: Center[], departments: Department[], agreements: Agreement[], media: MediaItem[] }>().props

    const [multimedia, setMultimedia] = useState({
        pool: media,
        am: [] as MediaItem[],
        pm: [] as MediaItem[],
    })

    const { data, setData, processing, errors, post } = useForm({
        title: '',
        start_at: '',
        end_at: '',
        centers: [] as string[],
        department_id: '',
        agreement_id: '',
    })
    console.log(media)
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
                <form id="form" method="post" onSubmit={handleSubmit} className="space-y-4" action="/campaigns">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label htmlFor="title" className="block text-sm font-medium text-gray-700">Título</label>
                            <Input
                                type="text"
                                id="title"
                                value={data.title}
                                name="title"
                                required
                                placeholder='Titulo de la campaña'
                                onChange={(e: React.ChangeEvent<HTMLInputElement>) => setData('title', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.title && <p className="text-red-500 text-sm mt-1">{errors.title}</p>}
                        </div>

                        <div>
                            <label htmlFor="department_id" className="block text-sm font-medium text-gray-700">Departamento</label>
                            <Select<Option, false>
                                options={optionsDepartment}
                                id="departments"
                                value={optionsDepartment.find(o => o.value === data.department_id) || null}
                                name="departments"
                                className="mt-1 rounded-md"
                                classNamePrefix="react-select"
                                onChange={(val) => setData('department_id', (val as Option | null)?.value ?? '')}
                                placeholder="Selecciona un departamento"
                                isClearable
                            />
                            {errors.department_id && <p className="text-red-500 text-sm mt-1">{errors.department_id}</p>}
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label htmlFor="start_date" className="block text-sm font-medium text-gray-700">Fecha y hora (inicio)</label>
                            <Input
                                type="datetime-local"
                                id="start_date"
                                value={data.start_at}
                                required
                                name="start_date"
                                onChange={e => setData('start_at', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.start_at && <p className="text-red-500 text-sm mt-1">{errors.start_at}</p>}
                        </div>

                        <div>
                            <label htmlFor="end_date" className="block text-sm font-medium text-gray-700">Fecha y hora (fin)</label>
                            <Input
                                type="datetime-local"
                                id="end_date"
                                value={data.end_at}
                                required
                                name="end_date"
                                onChange={e => setData('end_at', e.target.value)}
                                className="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-locatel-medio"
                            />
                            {errors.end_at && <p className="text-red-500 text-sm mt-1">{errors.end_at}</p>}
                        </div>
                    </div>

                    <div>
                        <label htmlFor="centers" className="block text-sm font-medium text-gray-700">Centros</label>
                        <Select<Option, true>
                            options={optionsCenter}
                            id="centers"
                            value={optionsCenter.filter(o => data.centers.includes(o.value))}
                            name="centers"
                            required
                            isMulti
                            className="mt-1 rounded-md"
                            classNamePrefix="react-select"
                            onChange={(val) => setData('centers', (val as Option[]).map(v => v.value))}
                            placeholder="Selecciona centros..."
                        />
                        {errors.centers && <p className="text-red-500 text-sm mt-1">{errors.centers}</p>}
                    </div>

                    <div>
                        <label htmlFor="agreements" className="block text-sm font-medium text-gray-700">Acuerdo</label>
                        <Select<Option, false>
                            options={optionsAgreement}
                            id="agreements"
                            value={optionsAgreement.find(o => o.value === data.agreement_id) || null}
                            name="agreements"
                            required
                            className="mt-1 rounded-md"
                            classNamePrefix="react-select"
                            onChange={(val) => setData('agreement_id', (val as Option | null)?.value ?? '')}
                            placeholder="Selecciona un acuerdo"
                            isClearable
                        />
                        {errors.agreement_id && <p className="text-red-500 text-sm mt-1">{errors.agreement_id}</p>}
                    </div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Multimedia</label>
                    <DragDropProvider onDragOver={(event) => {
                        setMultimedia((items) => move(items, event));
                    }}>
                        <div className='flex flex-col gap-6'>
                            {Object.entries(multimedia).map(([column, items]) => (
                                <DropZone key={column} id={column}>
                                    {items.map((id, index) => (
                                        <MediaItemElement item={id} index={index} column={column} />
                                    ))}
                                </DropZone>
                            ))}
                        </div>
                    </DragDropProvider>
                </form>

                <div className="flex flex-wrap justify-center gap-3">
                    <button
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