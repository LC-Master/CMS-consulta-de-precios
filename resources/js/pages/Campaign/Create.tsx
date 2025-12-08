import AppLayout from '@/layouts/app-layout'
import { Form, usePage } from '@inertiajs/react'


export default function CampaignCreate() {
    const props = usePage().props
    console.log(props)
    return (
        <AppLayout>
            <Form method='post' action='/campaigns'>
                <label htmlFor="title">Título:</label>
                <input type="text" id="title" name="title" />
                <label htmlFor="start_date">Fecha y hora (inicio):</label>
                <input type="datetime-local" id="start_date" name="start_date" />
                <label htmlFor="end_date">Fecha y hora (fin):</label>
                <input type="datetime-local" id="end_date" name="end_date" />

            </Form>
        </AppLayout>
    )
}