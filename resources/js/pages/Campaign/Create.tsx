import AppLayout from '@/layouts/app-layout'
import { Form } from '@inertiajs/react'


export default function CampaignCreate() {

    return (
        <AppLayout>
            <Form method='post' action='/campaigns'>
                <label htmlFor="title">Title:</label>
                <input type="text" id="title" name="title" />
            </Form>
        </AppLayout>
    )
}