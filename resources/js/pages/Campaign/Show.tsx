import { useForm } from '@inertiajs/react'
import React from 'react'
type UploadForm = {
    files: File[]
}

export default function Show() {
    const { data, setData, post, progress, errors } = useForm<UploadForm>({
        files: [],
    })

    const submit = (e: React.FormEvent) => {
        e.preventDefault()
        post('/video-upload')
    }
    return (
        <form
            onSubmit={submit}
            className="w-full max-w-xl mx-auto p-4 border rounded-lg space-y-4"
            onDragOver={(e) => e.preventDefault()}
            onDrop={(e) => {
                e.preventDefault()
                const droppedFiles = Array.from(e.dataTransfer.files)
                setData('files', [...data.files, ...droppedFiles])
            }}
        >
            <h2 className="text-lg font-semibold">Subir videos</h2>

            <input
                type="file"
                name='files[]'
                id='files'
                multiple
                onChange={(e) => {
                    if (!e.target.files) return
                    setData('files', [...data.files, ...Array.from(e.target.files)])
                }}
                className="block w-full text-sm text-gray-600"
            />

            {/* Preview */}
            {data.files.length > 0 ? (
                <div className="space-y-2">
                    {data.files.map((file, index) => (
                        <div key={index} className="p-2 border rounded text-sm">
                            <p><strong>Nombre:</strong> {file.name}</p>
                            <p><strong>Tamaño:</strong> {Math.round(file.size / 1024)} KB</p>
                            <p><strong>Tipo:</strong> {file.type || 'desconocido'}</p>

                            {file.type.startsWith('image/') && (
                                <img
                                    src={URL.createObjectURL(file)}
                                    alt="preview"
                                    className="mt-2 max-h-40"
                                />
                            )}
                        </div>
                    ))}
                </div>
            ) : (
                <p className="text-sm text-gray-500">
                    Arrastra archivos aquí o usa el selector
                </p>
            )}

            {errors.files && (
                <p className="text-sm text-red-600">{errors.files}</p>
            )}

            {progress && (
                <progress
                    value={progress.percentage}
                    max={100}
                    className="bg-blue-300 w-full"
                >
                    {progress.percentage}%
                </progress>
            )}

            <button
                type="submit"
                className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition disabled:opacity-50"
                disabled={data.files.length === 0}
            >
                Subir
            </button>
        </form>
    )
}
