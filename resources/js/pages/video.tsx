import { usePage } from "@inertiajs/react";
import styles from "../../css/input.module.css";

export default function Video() {
    const props = usePage()
    console.log(props)
    return (
        <>
            <section>
                <input type="text" className={styles.input_hidden} />
                <video autoPlay muted draggable={true}>
                    <source src="/files" type="video/mp4" />
                </video>
            </section>
            <div 
                onDragOver={(ev) => ev.preventDefault()} 
                onDrop={(ev) => {
                    ev.preventDefault();
                    const file = ev.dataTransfer?.files[0];
                    if (file) {
                        console.log('File dropped:', file);
                    } else {
                        console.log(file ? "File detected but not valid." : "No file detected.");
                    }
                }} 
                className={styles.container}
            >
            </div>
        </>

    )
}