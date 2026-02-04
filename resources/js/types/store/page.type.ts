import { Flash } from "../flash/flash.type";
import { Store } from "./index.type";

export type Props = {
    flash?: Flash;
    stores: { data: Store[] };
    filters: {
        status?: string;
        search?: string;
    };
};
