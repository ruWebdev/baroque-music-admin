import { defineStore } from 'pinia';

export const useOperasStore = defineStore('operas', {
  state: () => ({
    selectedLetter: null,
    searchQuery: '',
    isSearching: false,
    scrollTop: 0,
    lastVisitedOperaId: null,
    lastVisitedAt: 0,
    operas: [],
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
      this.lastVisitedOperaId = null;
      this.lastVisitedAt = 0;
      this.operas = [];
      this.searchResults = [];
      this.nextPage = null;
    },
    markVisited(id) {
      this.lastVisitedOperaId = id;
      this.lastVisitedAt = Date.now();
    },
  },
  persist: true,
});
