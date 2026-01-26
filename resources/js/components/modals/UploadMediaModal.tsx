import { createPortal } from 'react-dom'
import Modal from '@/components/Modal'
import { Button } from '@/components/ui/button'
import {
    useState,
    useRef,
    ChangeEvent,
    DragEvent,
    FormEvent,
} from 'react'
import { useForm } from '@inertiajs/react'
import { FormState } from '@/types/modal/index.type'
import { Send } from 'lucide-react'
import { Spinner } from '../ui/spinner'

export default function UploadMediaModal({ closeModal }: { closeModal: () => void }) {
    const {  setData, post, progress, errors, reset, transform, processing, cancel } = useForm<FormState>({
        files: [],
        thumbnails: [],
    })

    const [selectedFiles, setSelectedFiles] = useState<File[]>([])
    const [isDragging, setIsDragging] = useState(false)
    const inputRef = useRef<HTMLInputElement | null>(null)
    const syncFiles = (files: File[]) => {
        setSelectedFiles(files)
        setData('files', files)
    }
    const openFilePicker = () => inputRef.current?.click()
    const clearFiles = () => {
        if (inputRef.current) inputRef.current.value = ''
        setSelectedFiles([])
        reset()
    }
    const removeFile = (index: number) => {
        const next = selectedFiles.filter((_, i) => i !== index)
        syncFiles(next)
    }
    const onFilesChange = (e: ChangeEvent<HTMLInputElement>) => {
        const files = e.target.files ? Array.from(e.target.files) : []
        syncFiles(files)
        setData('thumbnails', [])
    }
    const handleDrop = (e: DragEvent) => {
        e.preventDefault()
        setIsDragging(false)

        const droppedFiles: File[] = []

        if (e.dataTransfer?.items?.length) {
            for (const item of Array.from(e.dataTransfer.items)) {
                if (item.kind === 'file') {
                    const f = item.getAsFile()
                    if (f) droppedFiles.push(f)
                }
            }
        } else if (e.dataTransfer?.files?.length) {
            droppedFiles.push(...Array.from(e.dataTransfer.files))
        }

        if (droppedFiles.length) {
            syncFiles([...selectedFiles, ...droppedFiles])
            setData('thumbnails', [])
            e.dataTransfer?.clearData()
        }
    }
    const handleDragOver = (e: DragEvent) => {
        e.preventDefault()
        setIsDragging(true)
    }
    const handleDragLeave = (e: DragEvent) => {
        e.preventDefault()
        setIsDragging(false)
    }
    const createVideoThumbnail = (file: File): Promise<File | null> =>
        new Promise((resolve) => {
            const url = URL.createObjectURL(file)
            const video = document.createElement('video')
            video.src = url
            video.preload = 'metadata'
            video.muted = true
            video.playsInline = true

            const cleanup = () => {
                URL.revokeObjectURL(url)
                video.remove()
            }

            video.addEventListener(
                'loadedmetadata',
                () => {
                    const seekTo = Math.min(0.5, video.duration / 2)
                    video.currentTime = seekTo

                    video.addEventListener(
                        'seeked',
                        () => {
                            const canvas = document.createElement('canvas')
                            canvas.width = 320
                            canvas.height = 180
                            const ctx = canvas.getContext('2d')
                            if (!ctx) return resolve(null)

                            ctx.drawImage(video, 0, 0, canvas.width, canvas.height)

                            canvas.toBlob(
                                (blob) => {
                                    cleanup()
                                    if (!blob) return resolve(null)
                                    resolve(
                                        new File([blob], `${file.name}-thumb.jpg`, {
                                            type: 'image/jpeg',
                                        })
                                    )
                                },
                                'image/jpeg',
                                0.8
                            )
                        },
                        { once: true }
                    )
                },
                { once: true }
            )

            video.addEventListener('error', () => {
                cleanup()
                resolve(null)
            })
        })
    const handleSubmit = async (e: FormEvent) => {
        e.preventDefault()
        e.stopPropagation()

        setData('files', selectedFiles)

        const thumbs: File[] = []

        for (const file of selectedFiles) {
            if (file.type.startsWith('video/')) {
                const thumb = await createVideoThumbnail(file)
                if (thumb) thumbs.push(thumb)
            }
        }

        transform((data) => ({
            ...data,
            thumbnails: thumbs,
        }))
        post('/media/upload', {
            forceFormData: true,
            only: ['media', 'flash'],
            reset: ['media', 'flash'],
            onSuccess: () => {
                clearFiles()
                closeModal()
            },
        })
    }
    return createPortal(
        <Modal actionWhenCloseTouchOutside={cancel} closeModal={closeModal}>
            <form id='uploadFilesForm' onSubmit={handleSubmit}>
                <h2 className="text-lg font-semibold mb-4">Agregar Multimedia</h2>

                <div
                    onDragOver={handleDragOver}
                    onDragEnter={handleDragOver}
                    onDragLeave={handleDragLeave}
                    onDrop={handleDrop}
                    className={`border-dashed border-2 p-4 rounded-md mb-4 ${isDragging ? 'border-blue-400 bg-blue-50' : 'border-gray-300'
                        }`}
                >
                    <input
                        ref={inputRef}
                        type="file"
                        multiple
                        className="hidden"
                        onChange={onFilesChange}
                    />

                    <div className="flex gap-3">
                        <Button type="button" disabled={processing} className='bg-locatel-medio hover:bg-locatel-oscuro' onClick={openFilePicker}>
                            Seleccionar archivos
                        </Button>

                        <Button type="button" disabled={processing} variant="secondary" onClick={clearFiles}>
                            Limpiar
                        </Button>
                    </div>

                    <div className="mt-3 text-sm text-gray-600">
                        {selectedFiles.length === 0 ? (
                            'Ningún archivo seleccionado'
                        ) : (
                            <ul className="mt-2 space-y-1">
                                {selectedFiles.map((f, i) => (
                                    <li key={i} className="flex justify-between">
                                        <span>{f.name}</span>
                                        <button
                                            type="button"
                                            className="text-red-500"
                                            onClick={() => removeFile(i)}
                                        >
                                            Quitar
                                        </button>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </div>
                </div>

                <div className="flex flex-col gap-2">
                    <div>
                        <p className='mb-4'>
                            Nota: los formatos aceptados son imágenes (JPG, PNG, webp) y videos (MP4).
                        </p>
                        {progress && (
                            <p className="text-sm">
                                Subiendo: {Math.round(progress.percentage || 0)}%
                            </p>
                        )}
                        {errors && (
                            Object.values(errors).flat().map((error: string, i) => (
                                <p key={i} className="text-red-500 text-sm">{error}</p>
                            ))

                        )}
                    </div>
                    <div className="flex gap-2">
                        <Button type="submit" className="w-1/2 bg-locatel-medio hover:bg-locatel-oscuro" disabled={selectedFiles.length === 0 || processing}>
                            {processing ? (<><Spinner/> Procesando...</>) : <>Guardar <Send /></>}
                        </Button>
                        <Button
                            type="submit"
                            form='uploadFilesForm'
                            variant="destructive"
                            className="w-1/2"
                            onClick={closeModal}
                        >
                            Cancelar
                        </Button>
                    </div>
                </div>
            </form>
        </Modal>,
        document.body
    )
}
