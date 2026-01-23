import { Head } from "@inertiajs/react";

export default function CenterIndex({ centers }: { centers: any[] }) {
    console.log(centers);

    return (
        <>
            <Head>
                <title>Centros</title>
            </Head>
            <h1>
                Centros
            </h1>
        </>

    )
}