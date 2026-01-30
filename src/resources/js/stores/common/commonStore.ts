import { create } from 'zustand';

interface CommonState {
	isLoading: boolean;
	setLoading: (loading: boolean) => void;
}

export const useCommonStore = create<CommonState>((set) => ({
	isLoading: false,
	setLoading: (loading) => set({ isLoading: loading }),
}));
