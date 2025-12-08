import AppLayout from '@/layouts/app-layout'
import { Form, usePage } from '@inertiajs/react'
import Select from 'react-select'

interface Center {
    code: string
    name: string
}

export default function CampaignCreate() {
    console.log(usePage().props)
    const { centers } = usePage<{ centers: Center[] }>().props

    const options = centers.map((center: Center) => {
        return { value: center.code, label: center.name }
    })
    return (
        <AppLayout>
            <form method='post' className='bg-black' action='/campaigns'>
                <label htmlFor="title">Título:</label>
                <input type="text" id="title" name="title" />
                <label htmlFor="start_date">Fecha y hora (inicio):</label>
                <input type="datetime-local" id="start_date" name="start_date" />
                <label htmlFor="end_date">Fecha y hora (fin):</label>
                <input type="datetime-local" id="end_date" name="end_date" />
                <label htmlFor="centers">Centros:</label>
                <Select options={options} id="centers" name="centers" />
            </form>
        </AppLayout>
    )
}