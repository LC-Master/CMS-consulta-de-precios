import { Link } from "@inertiajs/react";
import styles from "../../css/input.module.css";
import { useBear } from "@/context/bearer";

export default function Video() {
    const {bears,increasePopulation,removeAllBears,updateBears} = useBear();
    console.log(bears)
    return (
        <>
        <Link href="http://127.0.0.1/dashboard">ir a video</Link>
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
            <h1>{bears}</h1>
            <button onClick={() => increasePopulation()}>click me</button>
            <button onClick={()=> updateBears(109)}>clickcme 2</button>
            <button onClick={()=> removeAllBears()}>clickcme 3</button>

        </>

    )
}