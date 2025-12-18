import {create} from "zustand";

interface BearerState {
    sidebarOpen: boolean;
    increasePopulation: () => void;
    removeAllBears: () => void;
    updateBears: (newBears: number) => void;
}


export const useBear = create<BearerState>((set) => ({
  sidebarOpen: await cookieStore.get('sidebar_state') === 'true' || !await cookieStore.get('sidebar_state') ? true : false,
  increasePopulation: () => set((state) => ({ sidebarOpen: state.sidebarOpen + 1 })),
  removeAllBears: () => set({ sidebarOpen: 0 }),
  updateBears: (newBears) => set({ sidebarOpen: newBears }),
})