import { defineStore } from 'pinia';

export const useMainSettings = defineStore('mainSettings', {
    state: () => ({
        clients: {
            buyers: {},
            buyer_managers: {},
            sellers: {},
            addresses: {},
            marina_clients: {},
        },
        vehicles: {},
        drivers: {},
        deliveries: {
            discounts: {},
        },
    }),
    actions: {},
    persist: true,
});
