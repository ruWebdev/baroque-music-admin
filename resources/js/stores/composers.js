import { defineStore } from 'pinia';

export const useComposersStore = defineStore('composers', {
  state: () => ({
    // UI state
    selectedLetter: null,
    searchQuery: '',
    isSearching: false,
    scrollTop: 0,
    lastVisitedComposerId: null,
    lastVisitedAt: 0,

    // Data
    composers: [],
    searchResults: [],
    nextPage: null,
    pageSize: 50,
  }),
  actions: {
    reset() {
      this.selectedLetter = null;
      this.searchQuery = '';
      this.isSearching = false;
      this.scrollTop = 0;
      this.lastVisitedComposerId = null;
      this.lastVisitedAt = 0;
      this.composers = [];
      this.searchResults = [];
      this.nextPage = null;
      // keep pageSize as is
    },
    markVisited(id) {
      this.lastVisitedComposerId = id;
      this.lastVisitedAt = Date.now();
    },
  },
  persist: true,
});
