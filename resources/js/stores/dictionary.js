import { defineStore } from 'pinia';

export const useDictionaryStore = defineStore('dictionary', {
    state: () => ({
        selectedLetter: null,
        scrollTop: 0,
        lastVisitedDictionaryId: null,
        lastVisitedAt: 0,
    }),
    actions: {
        reset() {
            this.selectedLetter = null;
            this.scrollTop = 0;
            this.lastVisitedDictionaryId = null;
            this.lastVisitedAt = 0;
        },
        markVisited(id) {
            this.lastVisitedDictionaryId = id;
            this.lastVisitedAt = Date.now();
        },
    },
    persist: true,
});
