import { DataTable } from "@/components/DataTable";
import AppLayout from "@/layouts/app-layout";
import { index } from "@/routes/centertokens";
import { breadcrumbs } from "@/tools/breadcrumbs";
import { Column } from "@/types/datatable.types";
import { CenterToken, Props } from "@/types/centertokens/index.types";
import useToast from '@/hooks/use-toast';
import useModal from "@/hooks/use-modal";
import { useState } from "react";
import { Button } from "@/components/ui/button";
import DeleteCenterToken from "@/components/modals/DeleteCenterToken";

export default function CenterTokensIndex({ centerTokens, flash }: Props) {
    const [tokenId, setTokenId] = useState<number | null>(null);
    const { isOpen, closeModal, openModal } = useModal(false)
    const { ToastContainer } = useToast(flash);

    const columns: Column<CenterToken>[] = [
        {
            key: 'name',
            header: 'Título',
            render: (ct) => ct.name,
        },
        {
            key: 'center',
            header: 'Centro',
            render: (ct) => ct.center.name,
        },
        {
            key: 'last_used_at',
            header: 'Ultimo uso',
            render: (ct) => ct.last_used_at ? ct.last_used_at : 'Nunca',
        },
        {
            key: 'created_at',
            header: 'Creado',
            render: (ct) => ct.created_at,
        },
        {
            key: 'actions',
            header: 'Acción',
            render: (ct) => (
                <div className="flex gap-2">
                    <Button title='Eliminar token' onClick={() => {
                        setTokenId(ct.id);
                        openModal();
                    }} className='p-2 bg-red-600 h-8 text-white rounded-md'>
                        Revocar
                    </Button>
                </div>
            ),
        },
    ]
    return (
        <AppLayout breadcrumbs={breadcrumbs('Lista de tokens', index().url)}>
            {ToastContainer()}
            {isOpen && <DeleteCenterToken closeModal={closeModal} tokenId={tokenId!} />}
            <DataTable
                infiniteData="centerTokens"
                data={centerTokens.data}
                rowKey={(a) => a.id}
                columns={columns}
            />
        </AppLayout>
    )
}